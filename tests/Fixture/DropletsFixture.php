<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * DropletsFixture
 */
class DropletsFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        $this->records = [
            [
                'id' => 1,
                'bucket_id' => 1,
                'user_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'amount' => '123.48',
                'expense' => 1,
                'created' => '2026-05-19 13:43:18',
                'modified' => '2026-05-19 13:43:18',
                'occurred' => '2026-05-01',
            ],
        ];
        parent::init();
    }
}
