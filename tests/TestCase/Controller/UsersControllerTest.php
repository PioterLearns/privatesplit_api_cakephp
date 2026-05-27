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

    protected const DATA_DIR = __DIR__ . '/../../Fixture/ImportFiles/';

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
        //todo feels silly to me, but that's how they do it in
        // https://book.cakephp.org/6.x/development/testing.html#testing-a-json-responding-controller
        // rewrite this to something more sensible, like checking if valid Json + specific value comparisons.
        // with this even changed order of fields will make the test go belly up. At least canonicalize it...
        $expected = [
            'id' => 1,
            'username' => 'Alice',
            'created' => '2026-01-01T00:00:00+00:00',
            'modified' => '2026-01-01T00:00:00+00:00',
            "droplets" => [],
            "secondary_buckets" => [],
            "primary_buckets" => []
        ];
        $expected = json_encode($expected, JSON_PRETTY_PRINT);

        $this->get('/users/me');

        $this->assertResponseOk();
        $this->assertEquals($expected, (string)$this->_response->getBody());
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
    public function testAdd_correctDataProvided_responseOK(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userAToken',
            ],
        ]);
        $dataToAdd = [
            'username' => 'newuser',
            'password' => 'pass'
        ];

        $this->post('/users/register', $dataToAdd);

        $this->assertResponseOk();
    }

    public function testImportData_ValidDataProvided_responseOK(): void
    {
        $filePath = self::DATA_DIR . 'splitwise.valid.csv';
        $attachment = new \Laminas\Diactoros\UploadedFile(
            $filePath,
            filesize($filePath),
            \UPLOAD_ERR_OK,
            'whatever.csv',
            'text/csv',
        );
        $postData = [
            'type' => 'splitwise',
            'bucket_name' => 'TestBucket',
            'external_self_id' => 'primary user',
            'secondary_user_id' => 2,
            'primary_user_share' => 50,
            'attachment' => $attachment,
        ];
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userAToken',
            ]
        ]);

        $this->post('/users/importData', $postData);

        $this->assertResponseOk();
    }

}
