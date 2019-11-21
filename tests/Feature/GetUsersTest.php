<?php

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class GetUsersTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $validPassword = 'abcdef';
    protected $rootEmail;
    protected $rootToken;
    protected $adminToken;

    public function setUp(): void
    {
        parent::setUp();
        factory(App\Models\Root::class, 1)->create();
        factory(App\Models\Admin::class, 1)->create(['email' => 'testadmin@gmail.com']);
        factory(App\Models\Doctor::class, 1)->create();
        factory(App\Models\Patient::class, 1)->create();

        $this->rootEmail =  \config('mail.root');
        
        // Log root in
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "'.$this->validPassword.'", user: "Root") {
            token
          }
        }');
        $this->rootToken = $response->json('data.login.token');

        // Log admin in
        $response = $this->graphql('mutation {
          login(email: "testadmin@gmail.com", password: "'.$this->validPassword.'", user: "Admin") {
            token
          }
        }');
        $this->adminToken = $response->json('data.login.token');
    }

    public function testGetSingleAdmin()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);

        factory(App\Models\Admin::class, 1)->create();
        $response = $this->graphQL('{
          admin(id: 1) {
            id
          }
        }');

        $this->assertEquals(1, $response->json("data.admin.id"));
    }

    public function testGetSingleAdminNoToken()
    {
        $response = $this->graphQL('{
          admin(id: 1) {
            id
          }
        }');

        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetSingleAdminWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);

        $response = $this->graphQL('{
          admin(id: 1) {
           id
         }
        }');

        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetAllAdmins()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);

        factory(App\Models\Admin::class, 10)->create();
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

        $this->assertEquals(10, count($response->json("data.admins.data")));
        $this->assertEquals(12, $response->json("data.admins.paginatorInfo.total"));
    }

    public function testGetAllAdminsNoToken()
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

    public function testGetAllAdminsWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);
        
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
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetSinglePatient()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);
        $response = $this->graphQL('
      {
        patient(id: 1) {
            id
        }
      }
      ');

        $this->assertEquals(1, $response->json("data.patient.id"));
    }

    public function testGetSinglePatientNoToken()
    {
        $response = $this->graphQL('
      {
        patient(id: 1) {
            id
        }
      }
      ');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetAllPatients()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);

        factory(App\Models\Patient::class, 10)->create();
        $response = $this->graphQL('
      {
        patients(first: 10) {
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
        $this->assertEquals(10, count($response->json("data.patients.data")));
        $this->assertEquals(11, $response->json("data.patients.paginatorInfo.total"));
    }

    public function testGetAllPatientsNoToken()
    {
        $response = $this->graphQL('
      {
        patients(first: 10) {
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

    public function testGetAllPatientsWrongToken()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);
        $response = $this->graphQL('
      {
        patients(first: 10) {
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
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetSingleDoctor()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);
        $response = $this->graphQL('
      {
        doctor(id: 1) {
            id
        }
      }
      ');
        $this->assertEquals(1, $response->json("data.doctor.id"));
    }

    public function testGetSingleDoctorNoToken()
    {
        $response = $this->graphQL('
      {
        doctor(id: 1) {
            id
        }
      }
      ');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testGetAllDoctorsValidToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->rootToken
        ]);

        factory(App\Models\Doctor::class, 10)->create();
        $response = $this->graphQL('
      {
        doctors(first: 10) {
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
        $this->assertEquals(10, count($response->json("data.doctors.data")));
        $this->assertEquals(11, $response->json("data.doctors.paginatorInfo.total"));
    }

    public function testGetAllDoctorsNoToken()
    {
        $response = $this->graphQL('
      {
        doctors(first: 10) {
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

    public function testGetAllDoctorsWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);
        $response = $this->graphQL('
      {
        doctors(first: 10) {
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
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }
}
