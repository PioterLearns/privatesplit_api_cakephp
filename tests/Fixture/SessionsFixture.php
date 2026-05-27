<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\TestSuite\Fixture\TestFixture;

/**
 * SessionsFixture
 */
class SessionsFixture extends TestFixture
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
                'user_id' => 2,
                'token' => 'userBToken',
                'created' => '2026-05-24 13:11:49',
            ],
            [
                'id' => 2,
                'user_id' => 1,
                'token' => 'userAToken',
                'created' => '2026-05-24 13:11:49',
            ],
            [
                'id' => 3,
                'user_id' => 3,
                'token' => 'userCToken',
                'created' => '2026-05-24 13:11:49',
            ],
        ];
        parent::init();
    }
}
