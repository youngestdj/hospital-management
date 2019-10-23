<?php

use App\Utils\Helpers;
use Nuwave\Lighthouse\Testing\MakesGraphQLRequests;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Root;
use Crisu83\ShortId\ShortId;

class AccountVerificationTest extends TestCase
{
    use MakesGraphQLRequests;
    use RefreshDatabase;

    protected $rootEmail;

    public function setUp(): void
    {
        parent::setUp();
        $root = new Root();
        $this->rootEmail = $root->email = \config('mail.root');
        $shortid = ShortId::create();
        $root->verification_key = $shortid->generate() . $shortid->generate();
        $savedRoot = $root->save();
        $this->assertTrue($savedRoot);
    }

    public function testRootExists()
    {
        $response = $this->graphql("{
          admin(id: 1) {
            id
            email
          }
        }
        ");

        $this->assertEquals(1, $response->json("data.admin.id"));
        $this->assertEquals($this->rootEmail, $response->json("data.admin.email"));
    }

    public function testRootVerification()
    {
        $key = Helpers::getVerificationKey('Root', $this->rootEmail);
        $response = $this->graphQL('mutation { verifyRoot(key: "' . $key . '", password: "abcdef") }');
        $this->assertEquals('Root User has been verified. You can now log in with your email and password.', $response->json('data.verifyRoot'));
    }

    public function testRootVerificationWrongKey()
    {
        $response = $this->graphQL('mutation { verifyRoot(key: "wrongKey", password: "abcdef") }');
        $this->assertEquals('Invalid verification key.', $response->json('data.verifyRoot'));
    }

    public function testRootVerificationInvalidPassword()
    {
        $response = $this->graphQL('mutation { verifyRoot(key: "wrongKey", password: "abcde") }');
        $this->assertEquals('The password must be at least 6 characters.', $response->json('errors.0.extensions.validation.password.0'));
    }
}
