<?php

use PHPUnit\Framework\TestCase;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Service\OrcamentoService;
use App\Repository\OrcamentoRepository;

class OrcamentoServiceTest extends TestCase {

    public function testCriarOrcamentoComDadosValidos() {

        $repo = $this->createMock(OrcamentoRepository::class);
        $pdo = $this->createMock(\PDO::class);

        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('commit');
        $pdo->expects($this->never())->method('rollBack');

        $repo->expects($this->once())
            ->method('create')
            ->with('Teste', '2026-03-22')
            ->willReturn(1);

        $repo->expects($this->once())
            ->method('getProdutoById')
            ->with(1)
            ->willReturn([
                'id' => 1,
                'nome' => 'Produto 1',
                'valor' => 100.0
            ]);

        $repo->expects($this->once())
            ->method('addItem')
            ->with(1, 1, 1, 100.0);

        $repo->expects($this->once())
            ->method('updateTotal')
            ->with(1, 100.0);

        $service = new OrcamentoService($repo, $pdo);

        $data = [
            "nomeCliente" => "Teste",
            "data" => "2026-03-22",
            "itens" => [
                [
                    "produto_id" => 1,
                    "quantidade" => 1
                ]
            ]
        ];

        $id = $service->criarOrcamento($data);

        $this->assertIsInt($id);
        $this->assertGreaterThan(0, $id);
    }

    public function testCriarOrcamentoSemNome() {

        $repo = $this->createStub(OrcamentoRepository::class);
        $pdo = $this->createMock(\PDO::class);

        $pdo->expects($this->never())->method('beginTransaction');

        $service = new OrcamentoService($repo, $pdo);

        $data = [
            "nomeCliente" => "",
            "data" => "2026-03-22",
            "itens" => []
        ];

        $this->expectException(ValidationException::class);

        $service->criarOrcamento($data);
    }

    public function testProdutoNaoEncontrado() {

        $repo = $this->createMock(OrcamentoRepository::class);
        $pdo = $this->createMock(\PDO::class);

        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');
        $pdo->expects($this->never())->method('commit');

        $repo->expects($this->once())
            ->method('create')
            ->willReturn(1);

        $repo->expects($this->once())
            ->method('getProdutoById')
            ->willReturn(null);

        $repo->expects($this->never())->method('addItem');
        $repo->expects($this->never())->method('updateTotal');

        $service = new OrcamentoService($repo, $pdo);

        $data = [
            "nomeCliente" => "Teste",
            "data" => "2026-03-22",
            "itens" => [
                [
                    "produto_id" => 999,
                    "quantidade" => 1
                ]
            ]
        ];

        $this->expectException(NotFoundException::class);

        $service->criarOrcamento($data);
    }

    public function testErroFazRollback() {

        $repo = $this->createMock(OrcamentoRepository::class);
        $pdo = $this->createMock(\PDO::class);

        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');
        $pdo->expects($this->never())->method('commit');

        $repo->expects($this->once())
            ->method('create')
            ->willReturn(1);

        $repo->expects($this->once())
            ->method('getProdutoById')
            ->willThrowException(new \Exception("Erro no banco"));

        $service = new OrcamentoService($repo, $pdo);

        $data = [
            "nomeCliente" => "Teste",
            "data" => "2026-03-22",
            "itens" => [
                [
                    "produto_id" => 1,
                    "quantidade" => 1
                ]
            ]
        ];

        $this->expectException(\Exception::class);

        $service->criarOrcamento($data);
    }

    public function testListarOrcamentosComPaginacaoRetornaMeta() {

        $repo = $this->createMock(OrcamentoRepository::class);
        $pdo = $this->createStub(\PDO::class);

        $repo->expects($this->once())
            ->method('getOrcamentosPaginated')
            ->with(0, 2, null, null, null)
            ->willReturn([
                [
                    'id' => 1,
                    'nome_cliente' => 'Ana',
                    'data_solicitacao' => '2026-03-22',
                    'total' => 30.0,
                ],
            ]);

        $repo->expects($this->once())
            ->method('countOrcamentos')
            ->with(null, null, null)
            ->willReturn(1);

        $repo->expects($this->once())
            ->method('getItensByOrcamentoId')
            ->with(1)
            ->willReturn([
                [
                    'quantidade' => 1,
                    'subtotal' => 30.0,
                    'produto' => 'Mouse',
                    'valor' => 30.0,
                ],
            ]);

        $service = new OrcamentoService($repo, $pdo);

        $resultado = $service->listarOrcamentos([
            'page' => 1,
            'per_page' => 2,
        ]);

        $this->assertArrayHasKey('data', $resultado);
        $this->assertArrayHasKey('meta', $resultado);
        $this->assertSame(1, $resultado['meta']['page']);
        $this->assertSame(2, $resultado['meta']['per_page']);
        $this->assertSame(1, $resultado['meta']['total']);
        $this->assertSame(1, $resultado['meta']['total_pages']);
        $this->assertCount(1, $resultado['data']);
    }
}