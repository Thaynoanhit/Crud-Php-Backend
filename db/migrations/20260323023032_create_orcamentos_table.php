<?php

use Phinx\Migration\AbstractMigration;

class CreateOrcamentosTable extends AbstractMigration
{
    public function change()
    {
        if ($this->hasTable('orcamentos')) {
            return;
        }

        $table = $this->table('orcamentos');

        $table
            ->addColumn('nome_cliente', 'string', ['limit' => 255, 'null' => false])
            ->addColumn('data_solicitacao', 'date', ['null' => false])
            ->addColumn('total', 'decimal', ['precision' => 10, 'scale' => 2, 'default' => 0, 'null' => false])
            ->create();
    }
}