<?php

declare(strict_types=1);

use Migrations\BaseMigration;

class AddEncryption extends BaseMigration
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
        $table = $this->table('users');
        $table->addColumn('gpg', 'string', [
            'default' => null,
            'limit' => 64,
            'null' => false,//if we were in production, this would have to be true, to allow gradual user migration
        ])->update();


        //we could technically encrypt this data at this point, using just our service key, and re-encrypt it
        // as user keys come in, but again - we're not in production, so it would be a wasted effort, given that
        // our User changes don't even allow for any data to exist
        $table = $this->table('buckets');
        $table->changeColumn('name', 'text', [
            'default' => null,
            'null' => false,
        ])->update();
        $table->changeColumn('balance', 'text', [
            'default' => null,
            'null' => false,
        ])->update();


        $table = $this->table('droplets');
        $table->changeColumn('name', 'text', [
            'default' => null,
            'null' => false,
        ])->update();
        $table->changeColumn('amount', 'text', [
            'default' => null,
            'null' => false,
        ])->update();
    }
}
