<?php

use App\Utils\Helpers;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Crisu83\ShortId\ShortId;
use App\Models\Root;

class AuthTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $rootEmail;
    protected $validPassword;
    protected $invalidPassword;
    protected $token;

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
        $this->graphQL('mutation { verifyRoot(key: "' . $key . '", password: "abcdef") }');
    }

    public function testRootValidLogin()
    {
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "'.$this->validPassword.'", user: "Admin") {
            id,
            email
          }
        }');

        $this->assertEquals(1, $response->json("data.login.id"));
        $this->assertEquals($this->rootEmail, $response->json("data.login.email"));
        $this->token = $response->json('data.login.token');
    }

    public function testRootIncorrectEmail()
    {
        $response = $this->graphql('mutation {
          login(email: "fakeEmail@test.com", password: "'.$this->validPassword.'", user: "Admin") {
            id,
            email
          }
        }');
        $this->assertEquals('Invalid email or password.', $response->json('errors.0.message'));
    }

    public function testRootInvalidEmail()
    {
        $response = $this->graphql('mutation {
          login(email: "fakeEmailtest.com", password: "'.$this->validPassword.'", user: "Admin") {
            id,
            email
          }
        }');

        $this->assertEquals('Validation failed for the field [login].', $response->json('errors.0.message'));
        $this->assertEquals('The email must be a valid email address.', $response->json('errors.0.extensions.validation.email.0'));
    }

    public function testRootInvalidPassword()
    {
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "fakePassword", user: "Admin") {
            id,
            email
          }
        }');
        $this->assertEquals('Invalid email or password.', $response->json('errors.0.message'));
    }

    public function testRootInvalidUser()
    {
        $response = $this->graphql('mutation {
          login(email: "'.$this->rootEmail.'", password: "'.$this->validPassword.'", user: "fake") {
            id,
            email
          }
        }');
        $this->assertEquals('Validation failed for the field [login].', $response->json('errors.0.message'));
        $this->assertEquals('The selected user is invalid.', $response->json('errors.0.extensions.validation.user.0'));
    }
}
