<?php

use Phinx\Seed\AbstractSeed;

class ProdutosSeeder extends AbstractSeed
{
    public function run(): void
    {
        $produtos = [
            ['nome' => 'Entrada | Mouse USB 3200 DPI', 'valor' => 89.90, 'aliases' => ['Mouse']],
            ['nome' => 'Entrada | Teclado Membrana ABNT2', 'valor' => 149.90, 'aliases' => ['Teclado']],
            ['nome' => 'Entrada | Headset Stereo P2', 'valor' => 199.90, 'aliases' => ['Headseat']],
            ['nome' => 'Entrada | Gabinete Mid Tower Basico', 'valor' => 299.90, 'aliases' => ['Gabinete']],
            ['nome' => 'Entrada | Webcam Full HD 30FPS', 'valor' => 229.90, 'aliases' => ['Webcam Full HD']],
            ['nome' => 'Entrada | Mousepad Speed Grande', 'valor' => 59.90, 'aliases' => ['Mousepad Speed']],
            ['nome' => 'Entrada | Adaptador WiFi USB AC', 'valor' => 109.90, 'aliases' => ['Adaptador WiFi USB AC']],
            ['nome' => 'Entrada | SSD SATA 480GB', 'valor' => 239.90, 'aliases' => []],

            ['nome' => 'Intermediario | Mouse Gamer RGB', 'valor' => 199.90, 'aliases' => ['Mouse Gamer RGB']],
            ['nome' => 'Intermediario | Teclado Mecanico RGB', 'valor' => 399.90, 'aliases' => ['Teclado Mecanico RGB']],
            ['nome' => 'Intermediario | Headset Gamer 7.1', 'valor' => 499.90, 'aliases' => ['Headset Gamer 7.1']],
            ['nome' => 'Intermediario | Monitor 24 144Hz IPS', 'valor' => 1399.90, 'aliases' => ['Monitor 24 Polegadas 144Hz']],
            ['nome' => 'Intermediario | SSD NVMe 1TB', 'valor' => 449.90, 'aliases' => ['SSD NVMe 1TB']],
            ['nome' => 'Intermediario | HD 2TB 7200RPM', 'valor' => 389.90, 'aliases' => ['HD 2TB 7200RPM']],
            ['nome' => 'Intermediario | Memoria RAM 16GB DDR4', 'valor' => 359.90, 'aliases' => ['Memoria RAM 16GB DDR4']],
            ['nome' => 'Intermediario | Fonte 650W 80 Plus Bronze', 'valor' => 469.90, 'aliases' => ['Fonte 650W 80 Plus Bronze']],

            ['nome' => 'Alto Desempenho | Processador Ryzen 5 5600', 'valor' => 849.90, 'aliases' => ['Processador Ryzen 5 5600']],
            ['nome' => 'Alto Desempenho | Placa Mae B550', 'valor' => 799.90, 'aliases' => ['Placa Mae B550']],
            ['nome' => 'Alto Desempenho | Placa de Video RTX 4060 8GB', 'valor' => 2099.90, 'aliases' => ['Placa de Video RTX 4060 8GB']],
            ['nome' => 'Alto Desempenho | Cooler Air Tower 120mm', 'valor' => 229.90, 'aliases' => ['Cooler Air Tower 120mm']],
            ['nome' => 'Alto Desempenho | Notebook i5 16GB 512GB SSD', 'valor' => 4199.90, 'aliases' => ['Notebook i5 16GB 512GB SSD']],
            ['nome' => 'Alto Desempenho | Monitor 27 QHD 165Hz', 'valor' => 2299.90, 'aliases' => []],
            ['nome' => 'Alto Desempenho | Memoria RAM 32GB DDR5', 'valor' => 899.90, 'aliases' => []],
            ['nome' => 'Alto Desempenho | SSD NVMe 2TB PCIe 4.0', 'valor' => 999.90, 'aliases' => []],
        ];

        foreach ($produtos as $produto) {
            $nomeFinal = addslashes($produto['nome']);
            $valor = number_format((float) $produto['valor'], 2, '.', '');
            $aliases = $produto['aliases'] ?? [];

            foreach ($aliases as $alias) {
                $aliasEscapado = addslashes($alias);
                $this->execute("\n                    UPDATE produtos\n                    SET nome = '{$nomeFinal}', valor = {$valor}\n                    WHERE nome = '{$aliasEscapado}'\n                ");
            }

            $this->execute("\n                UPDATE produtos\n                SET valor = {$valor}\n                WHERE nome = '{$nomeFinal}'\n            ");

            $this->execute("\n                INSERT INTO produtos (nome, valor)\n                SELECT '{$nomeFinal}', {$valor}\n                WHERE NOT EXISTS (SELECT 1 FROM produtos WHERE nome = '{$nomeFinal}')\n            ");
        }
    }
}