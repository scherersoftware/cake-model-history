<?php
declare(strict_types = 1);
use Migrations\AbstractMigration;

class AddModelHistorySaveHash extends AbstractMigration
{

    public function up(): void
    {

        $this->table('model_history')
            ->addColumn('save_hash', 'string', [
                'after' => 'context_slug',
                'default' => null,
                'length' => 40,
                'null' => true,
            ])
            ->update();
    }

    public function down(): void
    {

        $this->table('model_history')
            ->removeColumn('save_hash')
            ->update();
    }
}
