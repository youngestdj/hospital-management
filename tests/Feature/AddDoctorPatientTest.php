<?php

use App\Utils\Helpers;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Admin;
use App\Models\Root;
use Crisu83\ShortId\ShortId;

class AddDoctorPatientTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $adminToken;
    protected $rootToken;

    public function setUp(): void
    {
        parent::setUp();
        // create new admin
        $admin = new Admin(['firstname' => 'Test', 'lastname' => 'admin']);
        $admin->email = 'admin@gmail.com';
        $this->validPassword = 'abcdef';
        $shortid = ShortId::create();
        $admin->verification_key = $shortid->generate() . $shortid->generate();
        $admin->save();

        // verify admin
        $key = Helpers::getVerificationKey('Admin', $admin->email);
        $this->graphQL('mutation { verifyUser(key: "' . $key . '", password: "'.$this->validPassword.'", user: "Admin") }');

        // Log admin in
        $response = $this->graphql('mutation {
          login(email: "'.$admin->email.'", password: "'.$this->validPassword.'", user: "Admin") {
            token
          }
        }');
        $this->adminToken = $response->json('data.login.token');

        // create new root user
        $root = new Root();
        $this->rootEmail = $root->email = \config('mail.root');
        $this->validRootPassword = 'abcdef';
        $shortid = ShortId::create();
        $root->verification_key = $shortid->generate() . $shortid->generate();
        $root->save();

        // verify root user
        $key = Helpers::getVerificationKey('Root', $this->rootEmail);
        $this->graphQL('mutation { verifyUser(key: "' . $key . '", password: "abcdef", user: "Root") }');

        // Log root in
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "'.$this->validRootPassword.'", user: "Root") {
            id,
            email,
            token
          }
        }');
        $this->rootToken = $response->json('data.login.token');
    }

    public function testAddDoctorInvalidDetails()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);

        $response = $this->graphql('mutation {
          addDoctor(
            email: "doctorgmail.com",
            firstname: "24323",
            lastname: "32434",
            phone: "1234",
            gender: "invalid",
            dob: "31413",
            specialization: "93rcj"            
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            specialization
          }
      }');

        $this->assertEquals('Validation failed for the field [addDoctor].', $response->json('errors.0.message'));
        $this->assertEquals('The email must be a valid email address.', $response->json('errors.0.extensions.validation.email.0'));
        $this->assertEquals('The firstname may only contain letters.', $response->json('errors.0.extensions.validation.firstname.0'));
        $this->assertEquals('The lastname may only contain letters.', $response->json('errors.0.extensions.validation.lastname.0'));
        $this->assertEquals('The phone must be at least 10 characters.', $response->json('errors.0.extensions.validation.phone.0'));
        $this->assertEquals('The selected gender is invalid.', $response->json('errors.0.extensions.validation.gender.0'));
        $this->assertEquals('The dob does not match the format d-m-Y.', $response->json('errors.0.extensions.validation.dob.0'));
        $this->assertEquals('The dob must be a date before today.', $response->json('errors.0.extensions.validation.dob.1'));
        $this->assertEquals('The specialization may only contain letters.', $response->json('errors.0.extensions.validation.specialization.0'));
    }

    public function testAddDoctorValidData()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);

        $response = $this->graphql('mutation {
          addDoctor(
            email: "doctor@gmail.com",
            firstname: "New",
            lastname: "Doctor",
            phone: "08102345670",
            gender: "female",
            dob: "12-02-1980",
            specialization: "Dermatology"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            specialization  
          }
      }');

        $this->assertEquals(1, $response->json("data.addDoctor.id"));
        $this->assertEquals("doctor@gmail.com", $response->json("data.addDoctor.email"));
        $this->assertEquals("New", $response->json("data.addDoctor.firstname"));
        $this->assertEquals("08102345670", $response->json("data.addDoctor.phone"));
        $this->assertEquals("female", $response->json("data.addDoctor.gender"));
        $this->assertEquals("12-02-1980", $response->json("data.addDoctor.dob"));
        $this->assertEquals("Dermatology", $response->json("data.addDoctor.specialization"));
        $this->assertEquals("Doctor", $response->json("data.addDoctor.lastname"));

        // test for duplicate data
        $response = $this->graphql('mutation {
          addDoctor(
            email: "doctor@gmail.com",
            firstname: "New",
            lastname: "Doctor",
            phone: "08102345678",
            gender: "female",
            dob: "12-02-1980",
            specialization: "Dermatology"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            specialization  
          }
        }');
        $this->assertEquals('Validation failed for the field [addDoctor].', $response->json('errors.0.message'));
        $this->assertEquals('The email has already been taken.', $response->json('errors.0.extensions.validation.email.0'));
    }

    public function testAddDoctorNoToken()
    {
        $response = $this->graphql('mutation {
        addDoctor(
          email: "doctor@gmail.com",
          firstname: "New",
          lastname: "Doctor",
          phone: "08102345678",
          gender: "female",
          dob: "12-02-1980",
          specialization: "Dermatology"
        ) {
          id,
          email,
          firstname,
          lastname,
          phone,
          gender,
          dob,
          specialization  
        }
      }');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testAddDoctorWrongToken()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->rootToken
      ]);
      
        $response = $this->graphql('mutation {
        addDoctor(
          email: "doctor@gmail.com",
          firstname: "New",
          lastname: "Doctor",
          phone: "08102345678",
          gender: "female",
          dob: "12-02-1980",
          specialization: "Dermatology"
        ) {
          id,
          email,
          firstname,
          lastname,
          phone,
          gender,
          dob,
          specialization  
        }
      }');
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testAddPatientInvalidDetails()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->adminToken
      ]);

        $response = $this->graphql('mutation {
          addPatient(
            email: "patientgmail.com",
            firstname: "24323",
            lastname: "32434",
            phone: "1234",
            gender: "invalid",
            dob: "31413",
            occupation: "2343",
            address: "no 1 test str",
            nationality: "2343",
            marital_status: "invalid",
            religion: "23432",
            ethnicity: "83r92"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            occupation,
            nationality,
            marital_status,
            religion,
            ethnicity
          }
      }');


        $this->assertEquals('Validation failed for the field [addPatient].', $response->json('errors.0.message'));
        $this->assertEquals('The email must be a valid email address.', $response->json('errors.0.extensions.validation.email.0'));
        $this->assertEquals('The firstname may only contain letters.', $response->json('errors.0.extensions.validation.firstname.0'));
        $this->assertEquals('The lastname may only contain letters.', $response->json('errors.0.extensions.validation.lastname.0'));
        $this->assertEquals('The phone must be at least 10 characters.', $response->json('errors.0.extensions.validation.phone.0'));
        $this->assertEquals('The selected gender is invalid.', $response->json('errors.0.extensions.validation.gender.0'));
        $this->assertEquals('The dob does not match the format d-m-Y.', $response->json('errors.0.extensions.validation.dob.0'));
        $this->assertEquals('The dob must be a date before today.', $response->json('errors.0.extensions.validation.dob.1'));
        $this->assertEquals('The nationality may only contain letters.', $response->json('errors.0.extensions.validation.nationality.0'));
        $this->assertEquals('The selected marital status is invalid.', $response->json('errors.0.extensions.validation.marital_status.0'));
        $this->assertEquals('The religion may only contain letters.', $response->json('errors.0.extensions.validation.religion.0'));
        $this->assertEquals('The ethnicity may only contain letters.', $response->json('errors.0.extensions.validation.ethnicity.0'));
    }

    public function testAddPatientValidData()
    {
        $this->withHeaders([
          'Content-Type' => 'Application/json',
          'Authorization' => $this->adminToken
        ]);

        $response = $this->graphql('mutation {
          addPatient(
            email: "patient@gmail.com",
            firstname: "Test",
            lastname: "Patient",
            phone: "09023456789",
            gender: "male",
            dob: "04-05-1999",
            occupation: "Sotware engineer",
            address: "no 1 test str",
            nationality: "Nigerian",
            marital_status: "single",
            religion: "Christianity",
            ethnicity: "Yoruba"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            occupation,
            address,
            nationality,
            marital_status,
            religion,
            ethnicity
          }
      }');

    
        $this->assertEquals(1, $response->json("data.addPatient.id"));
        $this->assertEquals("patient@gmail.com", $response->json("data.addPatient.email"));
        $this->assertEquals("Test", $response->json("data.addPatient.firstname"));
        $this->assertEquals("09023456789", $response->json("data.addPatient.phone"));
        $this->assertEquals("male", $response->json("data.addPatient.gender"));
        $this->assertEquals("04-05-1999", $response->json("data.addPatient.dob"));
        $this->assertEquals("Sotware engineer", $response->json("data.addPatient.occupation"));
        $this->assertEquals("Patient", $response->json("data.addPatient.lastname"));
        $this->assertEquals("no 1 test str", $response->json("data.addPatient.address"));
        $this->assertEquals("Nigerian", $response->json("data.addPatient.nationality"));
        $this->assertEquals("single", $response->json("data.addPatient.marital_status"));
        $this->assertEquals("Christianity", $response->json("data.addPatient.religion"));
        $this->assertEquals("Yoruba", $response->json("data.addPatient.ethnicity"));

        // test for duplicate email and phone
        $response = $this->graphql('mutation {
          addPatient(
            email: "patient@gmail.com",
            firstname: "Test",
            lastname: "Patient",
            phone: "09023456789",
            gender: "male",
            dob: "04-05-1999",
            occupation: "Sotware engineer",
            address: "no 1 test str",
            nationality: "Nigerian",
            marital_status: "single",
            religion: "Christianity",
            ethnicity: "Yoruba"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            occupation
          }
      }');
        $this->assertEquals('Validation failed for the field [addPatient].', $response->json('errors.0.message'));
        $this->assertEquals('The email has already been taken.', $response->json('errors.0.extensions.validation.email.0'));
        $this->assertEquals('The phone has already been taken.', $response->json('errors.0.extensions.validation.phone.0'));
    }

    public function testAddPatientNoToken()
    {
        $response = $this->graphql('mutation {
          addPatient(
            email: "patient@gmail.com",
            firstname: "Test",
            lastname: "Patient",
            phone: "09023456789",
            gender: "male",
            dob: "04-05-1999",
            occupation: "Sotware engineer",
            address: "no 1 test str",
            nationality: "Nigerian",
            marital_status: "single",
            religion: "Christianity",
            ethnicity: "Yoruba"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            occupation
          }
      }');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }

    public function testAddPatientWrongToken()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->rootToken
      ]);
      
        $response = $this->graphql('mutation {
          addPatient(
            email: "patient@gmail.com",
            firstname: "Test",
            lastname: "Patient",
            phone: "09023456789",
            gender: "male",
            dob: "04-05-1999",
            occupation: "Sotware engineer",
            address: "no 1 test str",
            nationality: "Nigerian",
            marital_status: "single",
            religion: "Christianity",
            ethnicity: "Yoruba"
          ) {
            id,
            email,
            firstname,
            lastname,
            phone,
            gender,
            dob,
            occupation
          }
      }');
        $this->assertEquals('You do not have permission to perform this action.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }
}
