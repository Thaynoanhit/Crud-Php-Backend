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

# 3. Rodar migrations
echo -e "${BLUE}3️⃣  Rodando migrations...${NC}"
docker compose exec php vendor/bin/phinx migrate
echo -e "${GREEN}✅ Migrations aplicadas${NC}"
echo ""

# 4. Rodar seeders
echo -e "${BLUE}4️⃣  Populando banco de dados...${NC}"
docker compose exec php vendor/bin/phinx seed:run
echo -e "${GREEN}✅ Dados inseridos${NC}"
echo ""

# 5. Rodar testes
echo -e "${BLUE}5️⃣  Executando testes...${NC}"
docker compose exec php vendor/bin/phpunit
echo -e "${GREEN}✅ Testes completados${NC}"
echo ""

# Mensagem final
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  ✨ Ambiente 100% pronto para uso!    ${NC}"
echo -e "${GREEN}========================================${NC}"
echo ""
echo "📚 Documentação da API (Scalar):"
echo "   http://localhost:8000/docs/"
echo ""
echo "📚 Documentação da API (Swagger):"
echo "   http://localhost:8000/docs/swagger.html"
echo ""
echo "🗄️  Banco de dados (phpMyAdmin):"
echo "   http://localhost:8080"
echo ""
echo "🔗 API direteamente:"
echo "   http://localhost:8000/orcamentos"
echo ""
echo "Tudo pronto! Clique nos links acima. 🎉"
