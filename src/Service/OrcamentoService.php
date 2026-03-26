<?php

namespace App\Service;

use App\Repository\OrcamentoRepository;
use App\Database\Connection;
use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use PDO;

class OrcamentoService {

    private OrcamentoRepository $repo;
    private PDO $pdo;

    public function __construct(?OrcamentoRepository $repo = null, ?PDO $pdo = null) {
        $pdo = $pdo ?? Connection::getConnection();

        $this->repo = $repo ?? new OrcamentoRepository($pdo);
        $this->pdo = $pdo;
    }

    private function validarItem($item) {

        if (empty($item['produto_id'])) {
            throw new ValidationException("Produto inválido");
        }

        if (empty($item['quantidade']) || $item['quantidade'] <= 0) {
            throw new ValidationException("Quantidade inválida");
        }
    }

    private function validarDadosOrcamento($data): void {

        if (empty($data['nomeCliente'])) {
            throw new ValidationException("Nome do cliente é obrigatório");
        }

        if (empty($data['data'])) {
            throw new ValidationException("Data é obrigatória");
        }

        if (!isset($data['itens']) || !is_array($data['itens']) || count($data['itens']) === 0) {
            throw new ValidationException("É necessário informar ao menos um item");
        }
    }

    public function criarOrcamento($data) {

        $this->validarDadosOrcamento($data);

        $this->pdo->beginTransaction();

        try {

            $orcamentoId = $this->repo->create(
                $data['nomeCliente'],
                $data['data']
            );

            $total = 0;

            foreach ($data['itens'] as $item) {

                $this->validarItem($item);

                $produto = $this->repo->getProdutoById($item['produto_id']);

                if (!$produto) {
                    throw new NotFoundException("Produto não encontrado");
                }

                $quantidade = (int) $item['quantidade'];
                $valor = (float) $produto['valor'];

                $subtotal = $quantidade * $valor;

                $this->repo->addItem(
                    $orcamentoId,
                    $item['produto_id'],
                    $quantidade,
                    $subtotal
                );

                $total += $subtotal;
            }

            $this->repo->updateTotal($orcamentoId, $total);

            $this->pdo->commit();

            return $orcamentoId;

        } catch (\Exception $e) {

            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listarOrcamentos(array $params = []) {
        $page = max(1, (int) ($params['page'] ?? 1));
        $perPage = (int) ($params['per_page'] ?? 10);
        if ($perPage < 1) {
            $perPage = 10;
        }
        $perPage = min($perPage, 100);

        $nomeCliente = $params['nome_cliente'] ?? null;
        $dataInicio = $params['data_inicio'] ?? null;
        $dataFim = $params['data_fim'] ?? null;

        $offset = ($page - 1) * $perPage;

        $orcamentos = $this->repo->getOrcamentosPaginated(
            $offset,
            $perPage,
            $nomeCliente,
            $dataInicio,
            $dataFim
        );

        $total = $this->repo->countOrcamentos($nomeCliente, $dataInicio, $dataFim);
        $totalPages = $total === 0 ? 0 : (int) ceil($total / $perPage);

        $resultado = [];

        foreach ($orcamentos as $orcamento) {

            $itens = $this->repo->getItensByOrcamentoId($orcamento['id']);

            $resultado[] = [
                "id" => (int) $orcamento['id'],
                "nome_cliente" => $orcamento['nome_cliente'],
                "data_solicitacao" => $orcamento['data_solicitacao'],
                "total" => (float) $orcamento['total'],
                "itens" => array_map(function ($item) {
                    return [
                        "quantidade" => (int) $item['quantidade'],
                        "subtotal" => (float) $item['subtotal'],
                        "produto" => $item['produto'],
                        "valor" => (float) $item['valor']
                    ];
                }, $itens)
            ];
        }

        return [
            'data' => $resultado,
            'meta' => [
                'page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => $totalPages,
            ],
        ];
    }

    public function buscarPorId($id) {

        $orcamento = $this->repo->getOrcamentoById($id);

        if (!$orcamento) {
            throw new NotFoundException("Orçamento não encontrado");
        }

        $itens = $this->repo->getItensByOrcamentoId($id);

        return [
            "id" => (int) $orcamento['id'],
            "nome_cliente" => $orcamento['nome_cliente'],
            "data_solicitacao" => $orcamento['data_solicitacao'],
            "total" => (float) $orcamento['total'],
            "itens" => array_map(function ($item) {
                return [
                    "quantidade" => (int) $item['quantidade'],
                    "subtotal" => (float) $item['subtotal'],
                    "produto" => $item['produto'],
                    "valor" => (float) $item['valor']
                ];
            }, $itens)
        ];
    }

    public function atualizarOrcamento($id, $data) {

        $this->validarDadosOrcamento($data);

        $orcamento = $this->repo->getOrcamentoById($id);

        if (!$orcamento) {
            throw new NotFoundException("Orçamento não encontrado");
        }

        try {

            $this->pdo->beginTransaction();

            $this->repo->deleteItensByOrcamentoId($id);

            $total = 0;

            foreach ($data['itens'] as $item) {

                $this->validarItem($item);

                $produto = $this->repo->getProdutoById($item['produto_id']);

                if (!$produto) {
                    throw new NotFoundException("Produto não encontrado");
                }

                $quantidade = (int) $item['quantidade'];
                $valor = (float) $produto['valor'];
                $subtotal = $valor * $quantidade;

                $total += $subtotal;

                $this->repo->addItem(
                    $id,
                    $item['produto_id'],
                    $quantidade,
                    $subtotal
                );
            }

            $this->repo->updateOrcamento(
                $id,
                $data['nomeCliente'],
                $data['data']
            );

            $this->repo->updateTotal($id, $total);

            $this->pdo->commit();

        } catch (\Exception $e) {

            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function deletarOrcamento($id) {
        $orcamento = $this->repo->getOrcamentoById($id);
        if (!$orcamento) {
            throw new NotFoundException("Orçamento não encontrado");
        }

        try {

            $this->pdo->beginTransaction();

            $this->repo->delete($id);

            $this->pdo->commit();

        } catch (\Exception $e) {

            $this->pdo->rollBack();
            throw $e;
        }
    }
}