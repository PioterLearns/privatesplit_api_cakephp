<?php
declare(strict_types=1);

namespace App\Test\Fixture;

use Cake\Core\Configure;
use Cake\TestSuite\Fixture\TestFixture;

/**
 * UsersFixture
 */
class UsersFixture extends TestFixture
{
    /**
     * Init method
     *
     * @return void
     */
    public function init(): void
    {
        //todo 0.4 again - this requires manual configuration. Add Command that will handle generating, and configuring
        // key for all test accounts
        $this->records = [
            [
                'id' => 1,
                'username' => 'Alice',
                'password' => password_hash('secret', PASSWORD_DEFAULT),
                'gpg' => Configure::read('Gpg.test.fingerprintA'),
                'created' => '2026-01-01 00:00:00',
                'modified' => '2026-01-01 00:00:00',
            ],
            [
                'id' => 2,
                'username' => 'Bob',
                'password' => password_hash('notsosecret', PASSWORD_DEFAULT),
                'gpg' => Configure::read('Gpg.test.fingerprintB'),
                'created' => '2026-01-01 00:00:00',
                'modified' => '2026-01-01 00:00:00',
            ],
            [
                'id' => 3,
                'username' => 'Charlie',
                'password' => password_hash('notsosecret', PASSWORD_DEFAULT),
                'gpg' => Configure::read('Gpg.test.fingerprintC'),
                'created' => '2026-01-01 00:00:00',
                'modified' => '2026-01-01 00:00:00',
            ],
        ];
        parent::init();
    }
}
