<?php

use Phinx\Migration\AbstractMigration;

class CreateProdutosTable extends AbstractMigration
{
    public function change()
    {
        if ($this->hasTable('produtos')) {
            return;
        }

        $table = $this->table('produtos');

        $table
            ->addColumn('nome', 'string', ['limit' => 255])
            ->addColumn('valor', 'decimal', ['precision' => 10, 'scale' => 2])
            ->create();
    }
}