<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddMissingKeys extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/en/migrations.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        //these were not taken care of by tutorial example, and since we're adding encryption that assumes user data
        // is cleared, we might as well add the missing keys now, so that we don't have to worry about potential
        // FK conflicts, that might arise if we have data

        $table = $this->table('buckets');
        $table->addForeignKey(
            'user_primary_id',
            'users',
            'id',
            ['delete' => 'cascade']
        )->update();
        $table->addForeignKey(
            'user_secondary_id',
            'users',
            'id',
            ['delete' => 'cascade']
        )->update();

        $table = $this->table('droplets');
        $table->addForeignKey(
            'user_id',
            'users',
            'id',
            ['delete' => 'cascade']
        )->update();
        $table->addForeignKey(
            'bucket_id',
            'buckets',
            'id',
            ['delete' => 'cascade']
        )->update();
    }
}
