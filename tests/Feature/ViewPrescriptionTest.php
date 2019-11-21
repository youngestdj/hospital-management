<?php

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ViewPrescriptionTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $validPassword = 'abcdef';
    protected $doctorToken;
    protected $patientToken;

    public function setUp(): void
    {
        parent::setUp();
        factory(App\Models\Doctor::class, 1)->create(['email' => 'testdoctor@gmail.com']);
        factory(App\Models\Patient::class, 1)->create(['email' => 'testpatient@gmail.com']);
        factory(App\Models\History::class, 1)->create();
        
        // Log Doctor in
        $response = $this->graphql('mutation {
          login(email: "testdoctor@gmail.com", password: "'.$this->validPassword.'", user: "Doctor") {
            token
          }
        }');
        $this->doctorToken = $response->json('data.login.token');

        // Log patient in
        $response = $this->graphql('mutation {
          login(email: "testpatient@gmail.com", password: "'.$this->validPassword.'", user: "Patient") {
            token
          }
        }');
        $this->patientToken = $response->json('data.login.token');
    }

    public function testGetPrescriptionValidToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->patientToken
        ]);

        factory(App\Models\History::class, 1)->create(['prescription' => 'Prescribed drug']);

        $response = $this->graphQL('{
          prescription(first: 5) {
            paginatorInfo {
              total,
            }
            data {
              id
              prescription
            }
          }
        }');

        $this->assertEquals(2, $response->json("data.prescription.paginatorInfo.total"));
        $this->assertEquals('Prescribed drug', $response->json("data.prescription.data.1.prescription"));
        $this->assertEquals(2, $response->json("data.prescription.data.1.id"));
    }

    public function testGetPrescriptionWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->doctorToken
        ]);

        factory(App\Models\History::class, 1)->create(['prescription' => 'Prescribed drug']);

        $response = $this->graphQL('{
          prescription(first: 5) {
            paginatorInfo {
              total,
            }
            data {
              id
              prescription
            }
          }
        }');

        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetPrescriptionNoToken()
    {
        $response = $this->graphQL('
      {
        admins(first: 10) {
          paginatorInfo {
            total,
            hasMorePages,
            currentPage,
            lastPage
          }
          data {
            id
          }
        }
      }
      ');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }
}
