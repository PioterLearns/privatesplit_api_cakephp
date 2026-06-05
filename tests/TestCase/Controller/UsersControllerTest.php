<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\UsersController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\UsersController Test Case
 *
 * @link \App\Controller\UsersController
 */
class UsersControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Users',
        'app.Sessions',
    ];

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\UsersController::view()
     */
    public function testMe_validAuthenticationProvided_correctUserIsFetched(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userAToken'
            ],
        ]);

        $expectedId = 1;

        $this->get('/users/me');

        $this->assertResponseOk();
        $this->assertEquals($expectedId, json_decode((string)$this->_response->getBody(), true)['id']);
    }

    public function testMe_invalidAuthenticationProvided_responseUnauthorized(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'invalidToken'
            ],
        ]);

        $this->get('/users/me');

        $this->assertResponseCode(401);
    }

    /**
     * Test register method
     *
     * @return void
     * @link \App\Controller\UsersController::register()
     */
    public function testRegister_correctDataProvided_responseOK(): void
    {
        $testKey = <<<PUBKEY
                     -----BEGIN PGP PUBLIC KEY BLOCK-----

                     mDMEahz5PxYJKwYBBAHaRw8BAQdAt6qeBK1vnb2DeUWnS+siUKtpBXce9qUja3C9
                     oewOr0q0B1Rlc3RBUEmIkAQTFgoAOBYhBO48kLS36td1mdcrGboioyVzNYOrBQJq
                     HPk/AhsDBQsJCAcCBhUKCQgLAgQWAgMBAh4BAheAAAoJELoioyVzNYOryWoBAKtX
                     Kqyqxc8mMYskB4tuK9eWpEX9hbnnEeFmhntnyQvNAP9qWNXgaanh4sHNmZOm1WY3
                     gGAqOWQCNWKUiUWeCGsmArg4BGoc+T8SCisGAQQBl1UBBQEBB0CQfsCi4ouPwjvJ
                     QiVdO/LsQcNsMXNIzI35vq3WAa4SMQMBCAeIeAQYFgoAIBYhBO48kLS36td1mdcr
                     GboioyVzNYOrBQJqHPk/AhsMAAoJELoioyVzNYOrZokA/1GeIVOqtixzaS08xrbT
                     e1PVKj2N1xkbB2+WpbDW4oAQAQDM4M4pDqzA6ne1caybb4Pl9KCU6ViSpv7Q+QkW
                     epCRBg==
                     =2xWK
                     -----END PGP PUBLIC KEY BLOCK-----
                     PUBKEY;

        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json'
            ],
        ]);
        $dataToAdd = [
            'username' => 'newuser',
            'password' => 'pass',
            'gpg' => $testKey
        ];

        $this->post('/users/register', $dataToAdd);

        $this->assertResponseOk();
    }
}
