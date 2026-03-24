<?php

namespace App\Service;

use App\Database\Connection;
use App\Repository\ProdutoRepository;
use PDO;

class ProdutoService
{
	private ProdutoRepository $repo;

	public function __construct(?ProdutoRepository $repo = null, ?PDO $pdo = null)
	{
		$pdo = $pdo ?? Connection::getConnection();
		$this->repo = $repo ?? new ProdutoRepository($pdo);
	}

	public function listarProdutos(): array
	{
		$produtos = $this->repo->getAll();

		return array_map(function ($produto) {
			return [
				'id' => (int) $produto['id'],
				'nome' => $produto['nome'],
				'valor' => (float) $produto['valor'],
			];
		}, $produtos);
	}
}

