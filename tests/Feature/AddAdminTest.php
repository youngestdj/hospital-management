<?php

use App\Utils\Helpers;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Root;
use Crisu83\ShortId\ShortId;

class AddAdminTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $rootEmail;
    protected $rootToken;
    protected $adminToken;

    public function setUp(): void
    {
        parent::setUp();
        // create new root user
        $root = new Root();
        $this->rootEmail = $root->email = \config('mail.root');
        $this->validPassword = 'abcdef';
        $shortid = ShortId::create();
        $root->verification_key = $shortid->generate() . $shortid->generate();
        $root->save();

        // verify root user
        $key = Helpers::getVerificationKey('Root', $this->rootEmail);
        $this->graphQL('mutation { verifyUser(key: "' . $key . '", password: "abcdef", user: "Root") }');

        // Log root in
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "'.$this->validPassword.'", user: "Root") {
            id,
            email,
            token
          }
        }');
        $this->rootToken = $response->json('data.login.token');
    }

    public function testAddAdminInvalidDetails()
    {
        $response = $this->graphql('mutation {
          addAdmin(email: "admingmail.com", firstname: "24323", lastname: "32434") {
            id,
            email,
            firstname,
            lastname
          }
      }');

        $this->assertEquals('Validation failed for the field [addAdmin].', $response->json('errors.0.message'));
        $this->assertEquals('The email must be a valid email address.', $response->json('errors.0.extensions.validation.email.0'));
        $this->assertEquals('The firstname may only contain letters.', $response->json('errors.0.extensions.validation.firstname.0'));
        $this->assertEquals('The lastname may only contain letters.', $response->json('errors.0.extensions.validation.lastname.0'));
    }

    public function testAddAdminValidData()
    {
        $this->withHeaders([
        'Content-Type' => 'Application/json',
        'Authorization' => $this->rootToken
      ]);
        $response = $this->graphql('mutation {
          addAdmin(email: "admin@gmail.com", firstname: "Firstname", lastname: "Lastname") {
            id,
            email,
            firstname,
            lastname
          }
      }');

        $this->assertEquals(2, $response->json("data.addAdmin.id"));
        $this->assertEquals("admin@gmail.com", $response->json("data.addAdmin.email"));
        $this->assertEquals("Firstname", $response->json("data.addAdmin.firstname"));
        $this->assertEquals("Lastname", $response->json("data.addAdmin.lastname"));

        // test for duplicate data
        $response = $this->graphql('mutation {
          addAdmin(email: "admin@gmail.com", firstname: "Firstname", lastname: "Lastname") {
            id,
            email,
            firstname,
            lastname
          }
      }');
        $this->assertEquals('Validation failed for the field [addAdmin].', $response->json('errors.0.message'));
        $this->assertEquals('The email has already been taken.', $response->json('errors.0.extensions.validation.email.0'));
    }

    public function testAddAdminNoToken()
    {
        $response = $this->graphql('mutation {
          addAdmin(email: "admin@gmail.com", firstname: "Firstname", lastname: "Lastname") {
            id,
            email,
            firstname,
            lastname
          }
      }');
        $this->assertEquals('Please log in first.', $response->json('errors.0.message'));
        $this->assertEquals('Authentication error.', $response->json('errors.0.extensions.error'));
    }
}
