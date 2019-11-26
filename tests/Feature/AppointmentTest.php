<?php

use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AppointmentTest extends TestCase
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
        factory(App\Models\Admin::class, 1)->create(['email' => 'testadmin@gmail.com']);
        
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

        // Log admin in
        $response = $this->graphql('mutation {
          login(email: "testadmin@gmail.com", password: "'.$this->validPassword.'", user: "Admin") {
            token
          }
        }');
        $this->adminToken = $response->json('data.login.token');
    }

    public function testBookAppointmentValidToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->patientToken
        ]);

        $response = $this->graphQL('mutation {
          bookAppointment (
          date: "12-12-2029 08:00 am"
          description: "This is just a test appointment."
        ) {
            id
            date
            description
          }
        }');

        $this->assertEquals(1, $response->json("data.bookAppointment.id"));
        $this->assertEquals('12-12-2029 08:00 am', $response->json("data.bookAppointment.date"));
        $this->assertEquals('This is just a test appointment.', $response->json("data.bookAppointment.description"));
    }

    public function testBookAppointmentWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->doctorToken
        ]);

        $response = $this->graphQL('mutation {
          bookAppointment (
          date: "12-12-2029 08:00 am"
          description: "This is just a test appointment."
        ) {
            id
        }
      }');

        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testBookAppointmentNoToken()
    {
        $response = $this->graphQL('mutation {
          bookAppointment (
        date: "12-12-2029 08:00 am"
        description: "This is just a test appointment."
      ) {
          id
        }
      }');

        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testBookAppointmentInvalidData()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->patientToken
      ]);

        $response = $this->graphQL('mutation {
          bookAppointment (
            date: ""
            description: ""
          ) {
              id
            }
        }');

        $this->assertEquals('Validation failed for the field [bookAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The description field is required.', $response->json('errors.0.extensions.validation.description.0'));
        $this->assertEquals('The date field is required.', $response->json('errors.0.extensions.validation.date.0'));
    }


    public function testBookAppointmentPastDate()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->patientToken
      ]);

        $response = $this->graphQL('mutation {
          bookAppointment (
            date: "12-12-2017 08:00 am"
            description: "This is just a test appointment."
          ) {
              id
            }
        }');

        $this->assertEquals('Validation failed for the field [bookAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The date must be a date after or equal to today.', $response->json('errors.0.extensions.validation.date.0'));
    }

    public function testBookAppointmentInvalidDateFormat()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->patientToken
      ]);

        $response = $this->graphQL('mutation {
          bookAppointment (
            date: "12-12-17 08:00 am"
            description: "This is just a test appointment."
          ) {
              id
            }
        }');

        $this->assertEquals('Validation failed for the field [bookAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The date must be a date after or equal to today.', $response->json('errors.0.extensions.validation.date.1'));
        $this->assertEquals('Date must be in the format dd-mm-yyyy hr:min am,pm', $response->json('errors.0.extensions.validation.date.0'));
    }



    public function testEditAppointmentValidToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);

        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
          editAppointment (
            id: 1
            doctor_id: 1
            date: "12-11-2099 08:00 am"
          ) {
              id
              date
            }
          }');

        $this->assertEquals(1, $response->json("data.editAppointment.id"));
        $this->assertEquals('12-11-2099 08:00 am', $response->json("data.editAppointment.date"));
    }

    public function testEditAppointmentWrongToken()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->doctorToken
        ]);

        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
          editAppointment (
            id: 1
            doctor_id: 1
            date: "12-11-2099 08:00 am"
          ) {
              id
            }
          }');

        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testEditAppointmentNoToken()
    {
        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
        editAppointment (
          id: 1
          doctor_id: 1
          date: "12-11-2099 08:00 am"
        ) {
            id
          }
        }');

        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testEditAppointmentInvalidData()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);

        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
          editAppointment (
            id: 1
            doctor_id: 1
            date: ""
          ) {
              id
              doctor_id
              date
            }
        }');

        $this->assertEquals('Validation failed for the field [editAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The date field is required.', $response->json('errors.0.extensions.validation.date.0'));
    }


    public function testEditAppointmentPastDate()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);

        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
          editAppointment (
            id: 1
            date: "12-12-2017 08:00 am"
            doctor_id: 1
          ) {
              id
            }
        }');

        $this->assertEquals('Validation failed for the field [editAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The date must be a date after or equal to today.', $response->json('errors.0.extensions.validation.date.0'));
    }

    public function testEditAppointmentInvalidDateFormat()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);

        factory(App\Models\Appointment::class, 1)->create();

        $response = $this->graphQL('mutation {
          editAppointment (
            id: 2
            doctor_id: 3
            date: "12-12-17 08:00 am"
          ) {
              id
            }
        }');

        $this->assertEquals('Validation failed for the field [editAppointment].', $response->json('errors.0.message'));
        $this->assertEquals('The date must be a date after or equal to today.', $response->json('errors.0.extensions.validation.date.1'));
        $this->assertEquals('Date must be in the format dd-mm-yyyy hr:min am,pm', $response->json('errors.0.extensions.validation.date.0'));
    }
}
