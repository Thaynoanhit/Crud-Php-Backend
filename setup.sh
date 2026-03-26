#!/bin/bash

# Script para rodar o projeto automaticamente
# Uso: bash setup.sh

set -e

echo "================================"
echo "  🚀 Setup Automático do Backend"
echo "================================"
echo ""

# Cores
GREEN='\033[0;32m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# 1. Subir containers
echo -e "${BLUE}1️⃣  Iniciando containers Docker...${NC}"
docker compose up -d
echo -e "${GREEN}✅ Containers iniciados${NC}"
echo ""

# 2. Aguardar MySQL
echo -e "${BLUE}2️⃣  Aguardando MySQL inicializar...${NC}"
echo "   (isso pode levar 15-20 segundos)"
sleep 20
echo -e "${GREEN}✅ MySQL pronto${NC}"
echo ""

# 3. Instalar dependencias PHP
echo -e "${BLUE}3️⃣  Instalando dependências PHP...${NC}"
if docker compose exec php sh -lc "command -v composer >/dev/null 2>&1"; then
  docker compose exec php composer install
else
  echo "ℹ️  Composer não encontrado no container php. Usando imagem composer:2..."
  docker run --rm \
    -u "$(id -u):$(id -g)" \
    -v "$PWD:/app" \
    -w /app \
    composer:2 install
fi
echo -e "${GREEN}✅ Dependências instaladas${NC}"
echo ""

# Garante que o Phinx esta disponivel antes de seguir.
if ! docker compose exec php test -f vendor/bin/phinx; then
  echo "❌ Erro: vendor/bin/phinx não encontrado no container php."
  echo "   Verifique se o pacote foi instalado no composer.json/composer.lock."
  exit 1
fi

# 4. Rodar migrations
echo -e "${BLUE}4️⃣  Rodando migrations...${NC}"
docker compose exec php vendor/bin/phinx migrate
echo -e "${GREEN}✅ Migrations aplicadas${NC}"
echo ""

# 5. Rodar seeders
echo -e "${BLUE}5️⃣  Populando banco de dados...${NC}"
docker compose exec php vendor/bin/phinx seed:run
echo -e "${GREEN}✅ Dados inseridos${NC}"
echo ""

# 6. Rodar testes
echo -e "${BLUE}6️⃣  Executando testes...${NC}"
docker compose exec -u "$(id -u):$(id -g)" php vendor/bin/phpunit
echo -e "${GREEN}✅ Testes completados${NC}"
echo ""

# Mensagem final
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  ✨ Ambiente 100% pronto para uso!    ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "📚 Documentação da API Interativa (Scalar):"
echo "   http://localhost:8000/docs/"
echo ""
echo "📚 Documentação da API Alternativa (Swagger):"
echo "   http://localhost:8000/docs/swagger.html"
echo ""
echo "🗄️  Banco de dados (phpMyAdmin):"
echo "   http://localhost:8080"
echo ""
echo "🔗 API direteamente:"
echo "   http://localhost:8000/orcamentos"
echo ""
echo "Tudo pronto! Clique nos links acima. 🎉"