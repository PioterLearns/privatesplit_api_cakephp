<?php

declare(strict_types=1);

namespace App\Test\TestCase\Controller;

use App\Controller\BucketsController;
use Cake\TestSuite\IntegrationTestTrait;
use Cake\TestSuite\TestCase;

/**
 * App\Controller\BucketsController Test Case
 *
 * @link \App\Controller\BucketsController
 */
class BucketsControllerTest extends TestCase
{
    use IntegrationTestTrait;

    /**
     * Fixtures
     *
     * @var array<string>
     */
    protected array $fixtures = [
        'app.Buckets',
        'app.Users',
    ];

    /**
     * Test index method
     *
     * @return void
     * @link \App\Controller\BucketsController::index()
     */
    public function testIndex_correctSessionProvided_responseOK(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);

        $this->get('/buckets/');

        $this->assertResponseOk();
    }

    public function testIndex_secondaryUserSessionProvided_responseOK(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testIndex_incorrectSessionProvided_response401(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);

        $this->get('/buckets/');

        $this->assertResponseCode(401);
    }

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\BucketsController::view()
     */
    public function testView_primaryUserSessionProvided_responseOK(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $id = 1;

        $this->get('/buckets/view/' . $id);

        $this->assertResponseOk();
    }

    public function testView_secondaryUserSessionProvided_responseOK(): void
    {
        $this->markTestIncomplete('Not implemented yet.');
    }

    public function testView_unauthorizedUserSessionProvided_responseForbidden(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $id = 1;

        $this->get('/buckets/view/' . $id);

        $this->assertResponseCode(403, "Unauthorized access to Bucket");
    }

    public function testView_invalidSessionProvided_responseUnauthorized(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $id = 1;

        $this->get('/buckets/view/' . $id);

        $this->assertResponseCode(401, "Unauthorized access to Bucket");
    }

    /**
     * Test add method
     *
     * @return void
     * @link \App\Controller\BucketsController::add()
     */
    public function testAdd_correctDataProvided_responseOK(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_primary_id' => 1,//todo move to session
            'user_secondary_id' => 2,
            'name' => 'someName',
        ];

        $this->post('/buckets/add', $dataToAdd);

        $this->assertResponseOk();
    }

    public function testAdd_sameUserIdInSecondary_response400(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_primary_id' => 1,//todo move to session
            'user_secondary_id' => 1,
            'name' => 'someName',
        ];

        $this->post('/buckets/add', $dataToAdd);

        $this->assertResponseCode(400, "Cannot share a bucket with yourself");
    }
}
