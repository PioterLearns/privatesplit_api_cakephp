<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateBuckets extends BaseMigration
{
    public bool $autoId = false;

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
        //todo FKs?
        $table = $this->table('buckets');
        $table->addColumn('id', 'integer', [
            'autoIncrement' => true,
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        //todo: user_primary_id and user_secondary_id break Cake's naming convention!
        //      Because they both point at Users, the convention of using user_id as FK can't be applied.
        //      At least not without re-structuring the design. We'll see if it's an issue later.
        $table->addColumn('user_primary_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('user_secondary_id', 'integer', [
            'default' => null,
            'limit' => 11,
            'null' => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('balance', 'string', [
            'default' => '0',
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('primary_user_share_percent', 'string', [
            'default' => '50',
            'limit' => 255,
            'null' => false,
        ]);
        $table->addColumn('created', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addColumn('modified', 'datetime', [
            'default' => null,
            'null' => false,
        ]);
        $table->addPrimaryKey([
            'id',
        ]);
        $table->create();
    }
}
