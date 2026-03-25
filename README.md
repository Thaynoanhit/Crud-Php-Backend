# 📊 Sistema de Gestão de Orçamentos

API REST em PHP para gerenciar orçamentos com múltiplos itens, cálculo automático de totais e persistência em MySQL.

### Opção Rápida (Recomendado)

```bash
bash setup.sh
```

Tudo será rodado automaticamente: containers, migrations, seeders e testes.

### Opção Manual

```bash
docker compose up -d
sleep 20
docker compose exec php vendor/bin/phinx migrate
docker compose exec php vendor/bin/phinx seed:run
docker compose exec php vendor/bin/phpunit
```

### Acessar a API

- 📚 **Documentação Interativa:** http://localhost:8000/docs/
- 📚 **Swagger (alternativo):** http://localhost:8000/docs/swagger.html
- 📊 **Banco de dados:** http://localhost:8080 (root/root)
- 🔗 **API:** http://localhost:8000/orcamentos

---

## 📦 O que faz

- ✅ Criar orçamento com nome do cliente, data e lista de produtos
- ✅ Listar produtos para preenchimento do formulário
- ✅ Cálculo automático do total
- ✅ Listar, buscar, atualizar e deletar orçamentos
- ✅ Paginação e filtros na listagem de orçamentos
- ✅ Validações de dados (cliente, data, produtos, quantidade)
- ✅ Transações ACID (rollback automático em caso de erro)

---

## 🧠 Arquitetura

```
Controller → Service → Repository → Database
```

- **Controller:** recebe requisições HTTP
- **Service:** regras de negócio + validações
- **Repository:** acesso ao banco via PDO

---

## 🔒 Validações

- Nome do cliente obrigatório
- Data obrigatória
- Mínimo 1 item no orçamento
- Produto deve existir
- Quantidade > 0

---

## 🧪 Testes

7 testes PHPUnit (Service + integração HTTP):

- ✅ Criação com sucesso
- ✅ Validação de dados inválidos
- ✅ Produto inexistente
- ✅ Rollback em erro
- ✅ Metadados de paginação
- ✅ Status HTTP 400 para JSON inválido
- ✅ Status HTTP 404 para rota inexistente

Uso de **mocks** para isolar BD. Rodados automaticamente em `bash setup.sh`.

---

## ⚙️ Stack

| Componente | Versão            |
| ---------- | ----------------- |
| PHP        | 8.4 (Apache)      |
| MySQL      | 8.0               |
| Phinx      | 0.16 (migrations) |
| PHPUnit    | 13 (testes)       |
| Docker     | Compose v2        |

---

## 🚀 Endpoints

| Método | Endpoint         | O quê             |
| ------ | ---------------- | ----------------- |
| GET    | /produtos        | Listar produtos   |
| POST   | /orcamentos      | Criar             |
| GET    | /orcamentos      | Listar            |
| GET    | /orcamentos/{id} | Buscar específico |
| PUT    | /orcamentos/{id} | Atualizar         |
| DELETE | /orcamentos/{id} | Deletar           |

Teste via Scalar: http://localhost:8000/docs/

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

---

## 🧯 Se algo der errado

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

---

**Desenvolvido como teste prático com foco em boas práticas e testes automatizados.**
