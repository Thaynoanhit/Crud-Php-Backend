<?php

namespace App\Controller;

use App\Helpers\ResponseHelper;
use App\Service\ProdutoService;

class ProdutoController
{
	public function index(): void
	{
		try {
			$service = new ProdutoService();
			ResponseHelper::success($service->listarProdutos());
		} catch (\Throwable $e) {
			ResponseHelper::error($e->getMessage(), 500);
		}
	}
}

