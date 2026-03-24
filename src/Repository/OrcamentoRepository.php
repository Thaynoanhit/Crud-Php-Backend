<?php

namespace App\Repository;

use PDO;

class OrcamentoRepository {

    private PDO $pdo;

    public function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function create($nomeCliente, $data): int {
        $stmt = $this->pdo->prepare("
            INSERT INTO orcamentos (nome_cliente, data_solicitacao, total)
            VALUES (:nome, :data, :total)
        ");

        $stmt->execute([
            'nome' => $nomeCliente,
            'data' => $data,
            'total' => 0
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function updateTotal($orcamentoId, $total) {
        $stmt = $this->pdo->prepare("
            UPDATE orcamentos 
            SET total = :total 
            WHERE id = :id
        ");

        $stmt->execute([
            'total' => $total,
            'id' => $orcamentoId
        ]);
    }

    public function addItem($orcamentoId, $produtoId, $quantidade, $subtotal) {
        $stmt = $this->pdo->prepare("
            INSERT INTO orcamento_itens 
            (orcamento_id, produto_id, quantidade, subtotal)
            VALUES (:orcamento, :produto, :qtd, :subtotal)
        ");

        $stmt->execute([
            'orcamento' => $orcamentoId,
            'produto' => $produtoId,
            'qtd' => $quantidade,
            'subtotal' => $subtotal
        ]);
    }

    public function getProdutoById($id) {

    $stmt = $this->pdo->prepare("
        SELECT id, nome, valor 
        FROM produtos 
        WHERE id = :id 
        LIMIT 1
    ");

    $stmt->execute(['id' => $id]);

    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    return $result ?: null;
    }

    public function getAllOrcamentos() {
        $stmt = $this->pdo->query("
            SELECT id, nome_cliente, data_solicitacao, total
            FROM orcamentos 
            ORDER BY data_solicitacao ASC, id ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrcamentosPaginated(
        int $offset,
        int $limit,
        ?string $nomeCliente = null,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): array {
        $conditions = [];
        $params = [
            'limit' => $limit,
            'offset' => $offset,
        ];

        if ($nomeCliente !== null && $nomeCliente !== '') {
            $conditions[] = 'nome_cliente LIKE :nome_cliente';
            $params['nome_cliente'] = '%' . $nomeCliente . '%';
        }

        if ($dataInicio !== null && $dataInicio !== '') {
            $conditions[] = 'data_solicitacao >= :data_inicio';
            $params['data_inicio'] = $dataInicio;
        }

        if ($dataFim !== null && $dataFim !== '') {
            $conditions[] = 'data_solicitacao <= :data_fim';
            $params['data_fim'] = $dataFim;
        }

        $where = '';
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "
            SELECT id, nome_cliente, data_solicitacao, total
            FROM orcamentos
            {$where}
            ORDER BY data_solicitacao ASC, id ASC
            LIMIT :limit OFFSET :offset
        ";

        $stmt = $this->pdo->prepare($sql);

        foreach ($params as $key => $value) {
            $paramType = PDO::PARAM_STR;
            if ($key === 'limit' || $key === 'offset') {
                $paramType = PDO::PARAM_INT;
            }

            $stmt->bindValue(':' . $key, $value, $paramType);
        }

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countOrcamentos(
        ?string $nomeCliente = null,
        ?string $dataInicio = null,
        ?string $dataFim = null
    ): int {
        $conditions = [];
        $params = [];

        if ($nomeCliente !== null && $nomeCliente !== '') {
            $conditions[] = 'nome_cliente LIKE :nome_cliente';
            $params['nome_cliente'] = '%' . $nomeCliente . '%';
        }

        if ($dataInicio !== null && $dataInicio !== '') {
            $conditions[] = 'data_solicitacao >= :data_inicio';
            $params['data_inicio'] = $dataInicio;
        }

        if ($dataFim !== null && $dataFim !== '') {
            $conditions[] = 'data_solicitacao <= :data_fim';
            $params['data_fim'] = $dataFim;
        }

        $where = '';
        if (!empty($conditions)) {
            $where = 'WHERE ' . implode(' AND ', $conditions);
        }

        $sql = "SELECT COUNT(*) as total FROM orcamentos {$where}";

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        return (int) ($row['total'] ?? 0);
    }

    public function getItensByOrcamentoId($orcamentoId) {
        $stmt = $this->pdo->prepare("
            SELECT 
                oi.quantidade,
                oi.subtotal,
                p.nome AS produto,
                p.valor
            FROM orcamento_itens oi
            INNER JOIN produtos p ON p.id = oi.produto_id
            WHERE oi.orcamento_id = :id
        ");

        $stmt->execute(['id' => $orcamentoId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOrcamentoById($id) {
        $stmt = $this->pdo->prepare("
            SELECT id, nome_cliente, data_solicitacao, total
            FROM orcamentos
            WHERE id = :id
            LIMIT 1
        ");

        $stmt->execute(['id' => $id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function deleteItensByOrcamentoId($id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM orcamento_itens 
            WHERE orcamento_id = :id
        ");

        $stmt->execute(['id' => $id]);
    }

    public function updateOrcamento($id, $nomeCliente, $data) {
        $stmt = $this->pdo->prepare("
            UPDATE orcamentos 
            SET nome_cliente = :nome, 
                data_solicitacao = :data 
            WHERE id = :id
        ");

        $stmt->execute([
            'nome' => $nomeCliente,
            'data' => $data,
            'id' => $id
        ]);
    }

    public function delete($id) {
        $stmt = $this->pdo->prepare("
            DELETE FROM orcamento_itens 
            WHERE orcamento_id = :id
        ");
        $stmt->execute(['id' => $id]);

        $stmt = $this->pdo->prepare("
            DELETE FROM orcamentos 
            WHERE id = :id
        ");
        $stmt->execute(['id' => $id]);
    }
}