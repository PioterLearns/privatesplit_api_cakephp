<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * BucketsFixture
 */
class BucketsFixture extends TestFixture
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
                'user_primary_id' => 1,
                'user_secondary_id' => 1,
                'name' => 'Lorem ipsum dolor sit amet',
                'balance' => 'Lorem ipsum dolor sit amet',
                'primary_user_share_percent' => 'Lorem ipsum dolor sit amet',
                'created' => '2026-05-19 13:43:14',
                'modified' => '2026-05-19 13:43:14',
            ],
        ];
        parent::init();
    }
}
