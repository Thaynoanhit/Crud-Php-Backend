<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Controller\OrcamentoController;
use App\Controller\ProdutoController;

if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}


$uri = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');
$method = $_SERVER['REQUEST_METHOD'];

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

if ($method === 'OPTIONS') {
    http_response_code(204);
    exit;
}


if ($uri === '/orcamentos' && $method === 'POST') {
    (new OrcamentoController())->store();
    exit;
}

if ($uri === '/orcamentos' && $method === 'GET') {
    (new OrcamentoController())->index($_GET);
    exit;
}

if ($uri === '/produtos' && $method === 'GET') {
    (new ProdutoController())->index();
    exit;
}

if (preg_match('/^\/orcamentos\/(\d+)$/', $uri, $matches) && $method === 'GET') {
    (new OrcamentoController())->show($matches[1]);
    exit;
}

if (preg_match('/^\/orcamentos\/(\d+)$/', $uri, $matches) && $method === 'PUT') {
    (new OrcamentoController())->update($matches[1]);
    exit;
}

if (preg_match('/^\/orcamentos\/(\d+)$/', $uri, $matches) && $method === 'DELETE') {
    (new OrcamentoController())->destroy($matches[1]);
    exit;
}

http_response_code(404);
echo json_encode([
    "success" => false,
    "error" => "Rota não encontrada"
]);