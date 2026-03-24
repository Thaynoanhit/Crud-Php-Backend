<?php

use Phinx\Seed\AbstractSeed;

class ProdutosSeeder extends AbstractSeed
{
    public function run(): void
    {
        $this->execute("\n            INSERT INTO produtos (nome, valor)\n            SELECT 'Mouse', 100.00\n            WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Mouse')\n        ");

        $this->execute("\n            INSERT INTO produtos (nome, valor)\n            SELECT 'Teclado', 200.00\n            WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Teclado')\n        ");

        $this->execute("\n            INSERT INTO produtos (nome, valor)\n            SELECT 'Headseat', 300.00\n            WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Headseat')\n        ");

        $this->execute("\n            INSERT INTO produtos (nome, valor)\n            SELECT 'Gabinete', 400.00\n            WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = 'Gabinete')\n        ");
    }
}