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
     *
     * @return void
     */
    public function run(): void
    {
        $data = [];

        $table = $this->table('buckets');
        $table->insert($data)->save();
    }
}
