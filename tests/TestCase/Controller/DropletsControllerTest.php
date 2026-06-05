<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\DropletsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\DropletsController Test Case
 *
 * @link \App\Controller\DropletsController
 */
class DropletsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Droplets',
        'app.Buckets',
        'app.Users',
        'app.Sessions',
    ];

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\DropletsController::view()
     */
    public function testView_primaryUserSessionProvided_responseOK(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userAToken',
            ],
        ]);
        $id = 1;

        $this->get('/droplets/' . $id);

        $this->assertResponseOk();
    }

    public function testView_secondaryUserSessionProvided_responseOK(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userBToken',
            ],
        ]);
        $id = 1;

        $this->get('/droplets/' . $id);

        $this->assertResponseOk();
    }

    public function testView_unauthorizedUserSessionProvided_responseForbidden(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userCToken',
            ],
        ]);
        $id = 1;

        $this->get('/droplets/' . $id);

        $this->assertResponseCode(403, "Unauthorized access to Bucket");
    }

    public function testView_invalidSessionProvided_responseUnauthorized(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'invalidToken',
            ],
        ]);
        $id = 1;

        $this->get('/droplets/' . $id);

        $this->assertResponseCode(401, "Unauthorized access to Bucket");
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\DropletsController::add()
     */
    public function testAdd_correctDataProvided_dropletAdded(): void
    {
        $this->configRequest([
            'headers' => [
                'Accept' => 'application/json',
                'Authorization' => 'userAToken',
            ],
        ]);
        $dataToAdd = [
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 1,
            'occurred' => '2026-06-03',
        ];

        $this->post('/droplets', $dataToAdd);

        $this->assertResponseOk();
    }

    /**
     * Test edit method
     *
     * @return void
     * @link \App\Controller\DropletsController::edit()
     */
    public function testEdit(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    /**
     * Test delete method
     *
     * @return void
     * @link \App\Controller\DropletsController::delete()
     */
    public function testDelete(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }
}
