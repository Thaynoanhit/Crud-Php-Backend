<?php

namespace App\Controller;

use App\Helpers\ResponseHelper;
use App\Service\ProdutoService;

class ProdutoController
{
	private function mapExceptionMessage(\Throwable $e): string
	{
		error_log($e);
		return "Erro interno do servidor";
	}

	public function index(): void
	{
		try {
			$service = new ProdutoService();
			ResponseHelper::success($service->listarProdutos());
		} catch (\Throwable $e) {
			ResponseHelper::error($this->mapExceptionMessage($e), 500);
		}
	}
}

