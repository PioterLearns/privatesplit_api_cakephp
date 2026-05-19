<?php

declare(strict_types=1);

use Migrations\BaseSeed;

/**
 * Buckets seed.
 */
class BucketsSeed extends BaseSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeds is available here:
     * https://book.cakephp.org/migrations/5/en/seeding.html
     * //todo change to https://book.cakephp.org/migrations/5/guides/seeding.html upstream?
     *
     * @return void
     */
    public function run(): void
    {
        $data = [
            [
                'id' => 1,
                'user_primary_id' => 1,
                'user_secondary_id' => 2,
                'name' => "EUR",
                'created' => date('Y-m-d H:i:s'),
                'modified' => date('Y-m-d H:i:s'),
            ],
        ];

        $table = $this->table('buckets');
        $table->insert($data)->save();
    }
}
