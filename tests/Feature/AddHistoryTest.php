<?php

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddHistoryTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $doctorToken;
    protected $adminToken;

    public function setUp(): void
    {
        parent::setUp();
        factory(App\Models\Admin::class, 1)->create(['email' => 'testadmin@example.com']);
        factory(App\Models\Doctor::class, 1)->create(['email' => 'doctor@example.com']);
        factory(App\Models\Patient::class, 1)->create(['email' => 'patient@example.com']);

        $password = 'abcdef';
      
        // Log doctor in
        $response = $this->graphql('mutation {
        login(email: "doctor@example.com", password: "'.$password.'", user: "Doctor") {
          id,
          email,
          token
        }
      }');
        $this->doctorToken = $response->json('data.login.token');

        // Log admin in
        $response = $this->graphql('mutation {
        login(email: "testadmin@example.com", password: "'.$password.'", user: "Admin") {
          id,
          email,
          token
        }
      }');
        $this->adminToken = $response->json('data.login.token');
    }

    public function testAddHistoryNoToken()
    {
        $response = $this->graphql('mutation {
          addHistory (
            patient_id: 1
            presenting_complaint: "bla bla bla"
            presenting_complaint_history: "Another bla bla bla"
            differential_diagnosis: "Test"
            diagnosis: "Real test"
            prescription: "Prescription"
            surgical_history: "History"
            social_history: "Social"
          ) {
            id,
            patient_id,
            prescription,
            patient {
              firstname
              lastname
            },
            doctor {
              firstname
              lastname
            }
          }
        }');

        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testAddHistoryInvalidData()
    {
        $response = $this->graphql('mutation {
        addHistory (
          patient_id: 10
          presenting_complaint: ""
          presenting_complaint_history: ""
          differential_diagnosis: ""
          diagnosis: ""
          prescription: ""
          surgical_history: ""
          social_history: ""
        ) {
          id,
          patient_id,
          presenting_complaint
          presenting_complaint_history
          prescription,
          differential_diagnosis
          surgical_history
          social_history
          diagnosis
          patient {
            firstname
            lastname
          },
          doctor {
            firstname
            lastname
          }
        }
      }');

        $this->assertEquals('Validation failed for the field [addHistory].', $response->json('errors.0.message'));
        $this->assertEquals('The selected patient id is invalid.', $response->json('errors.0.extensions.validation.patient_id.0'));
        $this->assertEquals('The presenting complaint field is required.', $response->json('errors.0.extensions.validation.presenting_complaint.0'));
        $this->assertEquals('The presenting complaint history field is required.', $response->json('errors.0.extensions.validation.presenting_complaint_history.0'));
        $this->assertEquals('The differential diagnosis field is required.', $response->json('errors.0.extensions.validation.differential_diagnosis.0'));
        $this->assertEquals('The diagnosis field is required.', $response->json('errors.0.extensions.validation.diagnosis.0'));
        $this->assertEquals('The prescription field is required.', $response->json('errors.0.extensions.validation.prescription.0'));
        $this->assertEquals('The surgical history field is required.', $response->json('errors.0.extensions.validation.surgical_history.0'));
        $this->assertEquals('The social history field is required.', $response->json('errors.0.extensions.validation.social_history.0'));
    }

    public function testAddHistoryValidData()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->doctorToken
        ]);

        $response = $this->graphql('mutation {
        addHistory (
          patient_id: 1
          presenting_complaint: "Test presenting complains"
          presenting_complaint_history: "Test presenting complains history"
          differential_diagnosis: "Test differential diagnosis"
          diagnosis: "Test diagnosis"
          prescription: "Test prescription"
          surgical_history: "Test surgical history"
          social_history: "Test social history"
        ) {
          id,
          patient_id,
          prescription,
          patient {
            email
          },
          doctor {
            email
          }
        }
      }');

      
        $this->assertEquals(1, $response->json("data.addHistory.id"));
        $this->assertEquals(1, $response->json("data.addHistory.patient_id"));
        $this->assertEquals("Test prescription", $response->json("data.addHistory.prescription"));
        $this->assertEquals("patient@example.com", $response->json("data.addHistory.patient.email"));
        $this->assertEquals("doctor@example.com", $response->json("data.addHistory.doctor.email"));
    }

    public function testAddHistoryWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);
        
        $response = $this->graphql('mutation {
        addHistory (
          patient_id: 1
          presenting_complaint: "Test presenting complains"
          presenting_complaint_history: "Test presenting complains history"
          differential_diagnosis: "Test differential diagnosis"
          diagnosis: "Test diagnosis"
          prescription: "Test prescription"
          surgical_history: "Test surgical history"
          social_history: "Test social history"
        ) {
          id,
          patient_id,
          prescription,
          patient {
            email
          },
          doctor {
            email
          }
        }
      }');
      
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }
}
