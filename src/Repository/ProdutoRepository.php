<?php

namespace App\Repository;

use PDO;

class ProdutoRepository
{
	private PDO $pdo;

	public function __construct(PDO $pdo)
	{
		$this->pdo = $pdo;
	}

	public function getAll(): array
	{
		$stmt = $this->pdo->query('SELECT id, nome, valor FROM produtos ORDER BY nome ASC');

		return $stmt->fetchAll(PDO::FETCH_ASSOC);
	}
}

