<?php

use PHPUnit\Framework\TestCase;

class ApiHttpStatusTest extends TestCase
{
    private const BASE_URL = 'http://localhost';

    private function request(string $method, string $path, ?string $body = null, array $headers = []): array
    {
        $httpHeaders = [];
        foreach ($headers as $name => $value) {
            $httpHeaders[] = $name . ': ' . $value;
        }

        $context = stream_context_create([
            'http' => [
                'method' => $method,
                'header' => implode("\r\n", $httpHeaders),
                'content' => $body ?? '',
                'ignore_errors' => true,
                'timeout' => 10,
            ],
        ]);

        $responseBody = file_get_contents(self::BASE_URL . $path, false, $context);
        if ($responseBody === false) {
            $responseBody = '';
        }

        $status = 0;
        if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $matches)) {
            $status = (int) $matches[1];
        }

        return [
            'status' => $status,
            'body' => $responseBody,
        ];
    }

    public function testRetorna400QuandoJsonInvalidoNoPostOrcamentos(): void
    {
        $response = $this->request('POST', '/orcamentos', 'xpto', [
            'Content-Type' => 'application/json',
        ]);

        $this->assertSame(400, $response['status']);
    }

    public function testRetorna404ParaRotaInexistente(): void
    {
        $response = $this->request('GET', '/rota-que-nao-existe');

        $this->assertSame(404, $response['status']);
    }
}
