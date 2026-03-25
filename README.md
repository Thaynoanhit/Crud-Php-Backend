# Sistema de Orcamentos - Backend

API REST em PHP para gerenciamento de orcamentos com itens, calculo automatico de total e persistencia em MySQL.

## O que faz

- Criar orcamento com nome do cliente, data e itens
- Listar produtos para preenchimento do formulario
- Calcular total automaticamente no backend
- Listar, buscar, atualizar e deletar orcamentos
- Paginar e filtrar listagem de orcamentos
- Validar dados obrigatorios e integridade dos itens
- Executar transacoes com rollback em caso de erro

## Arquitetura (Backend)

```
Controller -> Service -> Repository -> Database
```

- `Controller`: entrada HTTP e serializacao de resposta
- `Service`: regras de negocio, validacoes e transacoes
- `Repository`: acesso ao banco via PDO

## Pre-requisitos

- Docker + Docker Compose

## Setup

### Opcao rapida (recomendado)

```bash
bash setup.sh
```

Sobe containers, aplica migrations, executa seeds e roda testes.

### Opcao manual

```bash
docker compose up -d
sleep 20
docker compose exec php vendor/bin/phinx migrate
docker compose exec php vendor/bin/phinx seed:run
docker compose exec php vendor/bin/phpunit
```

## Enderecos locais

- Documentacao interativa: `http://localhost:8000/docs/`
- Swagger alternativo: `http://localhost:8000/docs/swagger.html`
- API base: `http://localhost:8000`
- PhpMyAdmin: `http://localhost:8080` (root/root)

## Validacoes

- Nome do cliente obrigatório
- Data obrigatória
- Mínimo 1 item no orçamento
- Produto deve existir
- Quantidade > 0

## Testes

8 testes PHPUnit (Service + integração HTTP):

- ✅ Criação com sucesso
- ✅ Validação de dados inválidos
- ✅ Produto inexistente
- ✅ Rollback em erro
- ✅ Metadados de paginação
- ✅ Normalização de data BR com hífen
- ✅ Status HTTP 400 para JSON inválido
- ✅ Status HTTP 404 para rota inexistente

Uso de **mocks** para isolar BD. Rodados automaticamente em `bash setup.sh`.

## Stack

| Componente | Versão            |
| ---------- | ----------------- |
| PHP        | 8.4               |
| MySQL      | 8.0               |
| Phinx      | 0.16 (migrations) |
| PHPUnit    | 13 (testes)       |
| Docker     | Compose v2        |

## Observacao de infra

Para simplificar o setup do teste, o serviço `php` usa imagem `php:8.4-apache`,
ou seja, Apache + PHP ficam unificados no mesmo container.

Essa abordagem atende ao requisito de infraestrutura (`Web + PHP + MySQL`) via
Docker Compose, com os papéis de `Web` e `PHP` consolidados em um unico servico
(`php` com Apache embutido) e o `MySQL` em servico dedicado.

Isso mantem o ambiente mais simples para avaliacao local, sem perder os
requisitos tecnicos do teste.

## Endpoints

| Método | Endpoint         | O quê             |
| ------ | ---------------- | ----------------- |
| GET    | /produtos        | Listar produtos   |
| POST   | /orcamentos      | Criar             |
| GET    | /orcamentos      | Listar            |
| GET    | /orcamentos/{id} | Buscar específico |
| PUT    | /orcamentos/{id} | Atualizar         |
| DELETE | /orcamentos/{id} | Deletar           |

Documentacao: `http://localhost:8000/docs/`

Observação: o `GET /orcamentos` retorna resultados em ordem crescente (`data_solicitacao ASC, id ASC`).

Filtros e paginação em `/orcamentos`:

```bash
GET /orcamentos?page=1&per_page=10
GET /orcamentos?nome_cliente=Ana
GET /orcamentos?data_inicio=2026-03-01&data_fim=2026-03-31
```

Exemplo de payload para `POST /orcamentos`:

```json
{
  "nomeCliente": "Maria",
  "data": "2026-03-23",
  "itens": [
    { "produto_id": 1, "quantidade": 2 },
    { "produto_id": 2, "quantidade": 1 }
  ]
}
```

## Troubleshooting

**MySQL ainda inicializando?**

```bash
sleep 30 && docker compose exec php vendor/bin/phinx migrate
```

**Limpar e começar do zero?**

```bash
docker compose down -v
bash setup.sh
```

**Ver logs?**

```bash
docker compose logs -f php
docker compose logs -f db
```
