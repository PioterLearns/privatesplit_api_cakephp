<?php

namespace App\Test\TestCase\Utility;

use Cake\TestSuite\TestCase;

class BalanceCalculatorTest extends TestCase
{


    //todo 0.4 change percentages in these tests from 50% to something else, so that they actually test proper split
    // also, re-write them from old e2e model
//    public function testAdd_primaryUserAddedExpense_amountProportionallyIncreasesBucketBalance(): void
//    {
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $dataToAdd = [
//            'bucket_id' => 1,
//            'name' => 'someName',
//            'amount' => 2,
//            'expense' => 1,
//            'occurred' => '2026-06-03',
//        ];
//
//        $this->post('/droplets/add', $dataToAdd);
//
//
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
//        $this->assertEquals("1", json_decode($this->_getBodyAsString(), true)['balance']);
//    }
//
//    public function testAdd_primaryUserAddedRepayment_amountFullyIncreasesBucketBalance(): void
//    {
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $dataToAdd = [
//            'bucket_id' => 1,
//            'name' => 'someName',
//            'amount' => 2,
//            'expense' => 0,
//            'occurred' => '2026-06-03',
//        ];
//
//        $this->post('/droplets/add', $dataToAdd);
//
//
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
//        $this->assertEquals("2", json_decode($this->_getBodyAsString(), true)['balance']);
//    }
//
//    public function testAdd_secondaryUserAddedExpense_amountProportionallyDecreasesBucketBalance(): void
//    {
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userBToken',
//            ],
//        ]);
//        $dataToAdd = [
//            'bucket_id' => 1,
//            'name' => 'someName',
//            'amount' => 2,
//            'expense' => 1,
//            'occurred' => '2026-06-03',
//        ];
//
//        $this->post('/droplets/add', $dataToAdd);
//
//
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
//        $this->assertEquals("-1", json_decode($this->_getBodyAsString(), true)['balance']);
//    }
//
//    public function testAdd_secondaryUserAddedRepayment_amountFullyDecreasesBucketBalance(): void
//    {
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userBToken',
//            ],
//        ]);
//        $dataToAdd = [
//            'bucket_id' => 1,
//            'name' => 'someName',
//            'amount' => 2,
//            'expense' => 0,
//            'occurred' => '2026-06-03',
//        ];
//
//        $this->post('/droplets/add', $dataToAdd);
//
//
//        $this->configRequest([
//            'headers' => [
//                'Accept' => 'application/json',
//                'Authorization' => 'userAToken',
//            ],
//        ]);
//        $this->get('/buckets/view/' . $dataToAdd['bucket_id']);
//        $this->assertEquals("-2", json_decode($this->_getBodyAsString(), true)['balance']);
//    }
}
