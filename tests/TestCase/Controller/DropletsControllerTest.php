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
    ];

    /**
     * Test view method
     *
     * @return void
     * @link \App\Controller\DropletsController::view()
     */
    public function testView_primaryUserSessionProvided_responseOK(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $id = 1;

        $this->get('/droplets/view/' . $id);

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

        $this->get('/droplets/view/' . $id);

        $this->assertResponseCode(403, "Unauthorized access to Bucket");
    }

    public function testView_invalidSessionProvided_responseUnauthorized(): void
    {
        //todo provide session
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $id = 1;

        $this->get('/droplets/view/' . $id);

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
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_id' => 1,//todo move to session
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 1
        ];

        $this->post('/droplets/add', $dataToAdd);

        $this->assertResponseOk();
    }

    public function testAdd_primaryUserAddedExpense_amountProportionallyIncreasesBucketBalance(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_id' => 1,//todo move to session
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 1
        ];

        $this->post('/droplets/add', $dataToAdd);

        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
        $this->assertEquals("1", json_decode($this->_getBodyAsString(), true)['balance']);
    }

    public function testAdd_primaryUserAddedRepayment_amountFullyIncreasesBucketBalance(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_id' => 1,//todo move to session
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 0
        ];

        $this->post('/droplets/add', $dataToAdd);

        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
        $this->assertEquals("2", json_decode($this->_getBodyAsString(), true)['balance']);
    }

    public function testAdd_secondaryUserAddedExpense_amountProportionallyDecreasesBucketBalance(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_id' => 2,//todo move to session
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 1
        ];

        $this->post('/droplets/add', $dataToAdd);

        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
        $this->assertEquals("-1", json_decode($this->_getBodyAsString(), true)['balance']);
    }

    public function testAdd_secondaryUserAddedRepayment_amountFullyDecreasesBucketBalance(): void
    {
        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $dataToAdd = [
            'user_id' => 2,//todo move to session
            'bucket_id' => 1,
            'name' => 'someName',
            'amount' => 2,
            'expense' => 0
        ];

        $this->post('/droplets/add', $dataToAdd);

        $this->configRequest([
            'headers' => ['Accept' => 'application/json'],
        ]);
        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
        $this->assertEquals("-2", json_decode($this->_getBodyAsString(), true)['balance']);
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
