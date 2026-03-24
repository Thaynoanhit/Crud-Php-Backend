<?php

namespace App\Controller;

use App\Exceptions\NotFoundException;
use App\Exceptions\ValidationException;
use App\Service\OrcamentoService;
use App\Helpers\ResponseHelper;

class OrcamentoController {

    private function mapExceptionStatus(\Throwable $e): int
    {
        if ($e instanceof ValidationException) {
            return 400;
        }

        if ($e instanceof NotFoundException) {
            return 404;
        }

        return 500;
    }

    public function store() {

        try {

            $data = json_decode(file_get_contents("php://input"), true);

            if (!is_array($data)) {
                throw new ValidationException("JSON inválido");
            }

            $service = new OrcamentoService();
            $id = $service->criarOrcamento($data);

            ResponseHelper::success(
                ["id" => $id],
                "Orçamento criado com sucesso",
                201
            );

        } catch (\Throwable $e) {
            ResponseHelper::error($e->getMessage(), $this->mapExceptionStatus($e));
        }
    }

    public function index(array $queryParams = []) {
        try {
            $service = new OrcamentoService();
            ResponseHelper::success($service->listarOrcamentos($queryParams));
        } catch (\Throwable $e) {
            ResponseHelper::error($e->getMessage(), $this->mapExceptionStatus($e));
        }
    }

    public function show($id) {
        try {
            $service = new OrcamentoService();

            ResponseHelper::success($service->buscarPorId($id));

        } catch (\Throwable $e) {
            ResponseHelper::error($e->getMessage(), $this->mapExceptionStatus($e));
        }
    }

    public function update($id) {
        try {
            $data = json_decode(file_get_contents("php://input"), true);

            if (!is_array($data)) {
                throw new ValidationException("JSON inválido");
            }

            $service = new OrcamentoService();
            $service->atualizarOrcamento($id, $data);

            ResponseHelper::success(null, "Orçamento atualizado");

        } catch (\Throwable $e) {
            ResponseHelper::error($e->getMessage(), $this->mapExceptionStatus($e));
        }
    }

    public function destroy($id) {
        try {
            $service = new OrcamentoService();
            $service->deletarOrcamento($id);

            ResponseHelper::success(null, "Orçamento deletado");

        } catch (\Throwable $e) {
            ResponseHelper::error($e->getMessage(), $this->mapExceptionStatus($e));
        }
    }
}