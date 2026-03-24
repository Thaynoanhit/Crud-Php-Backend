<?php

use Phinx\Migration\AbstractMigration;

class CreateOrcamentoItensTable extends AbstractMigration
{
    public function change()
    {
        if (!$this->hasTable('orcamento_itens')) {
            $table = $this->table('orcamento_itens');

            $table
                ->addColumn('orcamento_id', 'integer', ['signed' => false])
                ->addColumn('produto_id', 'integer', ['signed' => false])
                ->addColumn('quantidade', 'integer')
                ->addColumn('subtotal', 'decimal', ['precision' => 10, 'scale' => 2])
                ->addColumn('created_at', 'timestamp', [
                    'default' => 'CURRENT_TIMESTAMP'
                ])
                ->addForeignKey('orcamento_id', 'orcamentos', 'id', [
                    'delete' => 'CASCADE'
                ])
                ->addForeignKey('produto_id', 'produtos', 'id', [
                    'delete' => 'RESTRICT'
                ])
                ->create();

            return;
        }

        // Corrige estrutura parcial criada anteriormente sem constraints.
        $table = $this->table('orcamento_itens');
        $table
            ->changeColumn('orcamento_id', 'integer', ['signed' => false, 'null' => true])
            ->changeColumn('produto_id', 'integer', ['signed' => false, 'null' => true])
            ->update();

        if (!$table->hasForeignKey(['orcamento_id'])) {
            $table
                ->addForeignKey('orcamento_id', 'orcamentos', 'id', ['delete' => 'CASCADE'])
                ->update();
        }

        if (!$table->hasForeignKey(['produto_id'])) {
            $table
                ->addForeignKey('produto_id', 'produtos', 'id', ['delete' => 'RESTRICT'])
                ->update();
        }
    }
}