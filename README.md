# ConsigNow API

API RESTful desenvolvida em Laravel 12 para gerenciamento de vendas consignadas, estoque e condicionais.

## 📋 Pré-requisitos

- **PHP** >= 8.2
- **Composer** >= 2.0
- **Node.js** >= 18 (para assets Vite)
- **SQLite** ou outro banco de dados suportado

## 🚀 Instalação

### 1. Clonar o repositório

```bash
git clone <url-do-repositorio>
cd ConsigNowAPI
```

### 2. Instalar dependências PHP

```bash
composer install
```

### 3. Instalar dependências Node.js (opcional, para assets)

```bash
npm install
```

### 4. Configurar variáveis de ambiente

```bash
# Copiar o arquivo de exemplo
cp .env.example .env

# Gerar a chave da aplicação
php artisan key:generate
```

### 5. Configurar o banco de dados

Por padrão, a API usa SQLite. O arquivo será criado automaticamente.

```bash
# Criar o arquivo do banco de dados (SQLite)
touch database/database.sqlite

# Executar as migrations
php artisan migrate
```

**Opção MySQL/PostgreSQL:**
Edite o arquivo `.env` com suas credenciais:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=consignow
DB_USERNAME=seu_usuario
DB_PASSWORD=sua_senha
```

### 6. Executar seeders (opcional)

```bash
php artisan db:seed
```

## ▶️ Executando o Projeto

### Modo Desenvolvimento (simples)

```bash
php artisan serve
```

A API estará disponível em: `http://localhost:8000`

### Modo Desenvolvimento (completo com queues e logs)

```bash
composer dev
```

Este comando inicia simultaneamente:
- Servidor HTTP
- Queue listener
- Laravel Pail (logs em tempo real)
- Vite (compilação de assets)

## 🔐 Autenticação

A API utiliza **Laravel Sanctum** para autenticação via tokens.

### Endpoints de Autenticação

| Método | Endpoint | Descrição |
|--------|----------|-----------|
| POST | `/api/register` | Registrar novo usuário |
| POST | `/api/login` | Fazer login e obter token |
| POST | `/api/logout` | Fazer logout (autenticado) |
| GET | `/api/perfil` | Obter perfil do usuário (autenticado) |

### Exemplo de Login

```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email": "usuario@email.com", "password": "senha123"}'
```

### Usando o Token

```bash
curl -X GET http://localhost:8000/api/perfil \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

## 📚 Endpoints da API

Todos os endpoints abaixo requerem autenticação (Bearer Token).

### Produtos
- `GET /api/produto` - Listar produtos
- `GET /api/produto/{id}` - Obter produto
- `POST /api/produto` - Criar produto
- `PUT /api/produto/{id}` - Atualizar produto
- `DELETE /api/produto/{id}` - Excluir produto

### Categorias
- `GET /api/categoria` - Listar categorias

### Condicionais
- `GET /api/condicional` - Listar condicionais
- `GET /api/condicional/{id}` - Obter condicional
- `POST /api/condicional` - Criar condicional
- `PUT /api/condicional/{id}` - Atualizar condicional
- `DELETE /api/condicional/{id}` - Excluir condicional
- `POST /api/condicional/{id}/itens` - Adicionar item
- `GET /api/condicional/{id}/itens` - Listar itens
- `PUT /api/condicional/{id}/itens/devolver` - Devolver itens
- `PUT /api/condicional/{id}/itens/{itemId}` - Atualizar item
- `DELETE /api/condicional/{id}/itens/{itemId}` - Remover item

### Vendas
- `GET /api/vendas` - Listar vendas
- `GET /api/vendas/{id}` - Obter venda
- `POST /api/vendas` - Criar venda
- `PUT /api/vendas/{id}` - Atualizar venda
- `PUT /api/vendas/{id}/finalizar` - Finalizar venda
- `DELETE /api/vendas/{id}` - Excluir venda
- `POST /api/vendas/{vendaId}/itens` - Adicionar item à venda
- `GET /api/vendas/{vendaId}/itens` - Listar itens da venda
- `DELETE /api/vendas/{vendaId}/itens/{vendaItemId}` - Remover item

### Almoxarifados
- `GET /api/almoxarifados` - Listar almoxarifados
- `GET /api/almoxarifados/{id}` - Obter almoxarifado
- `POST /api/almoxarifados` - Criar almoxarifado
- `DELETE /api/almoxarifados/{id}` - Excluir almoxarifado

### Estoques
- `GET /api/estoques` - Listar estoques
- `GET /api/estoques/{id}` - Obter estoque
- `POST /api/estoques` - Criar estoque
- `DELETE /api/estoques/{id}` - Excluir estoque

## 🧪 Testes

```bash
# Executar todos os testes
php artisan test

# Ou via composer
composer test
```

## 📁 Estrutura do Projeto

```
ConsigNowAPI/
├── app/
│   ├── Http/Controllers/    # Controllers da API
│   └── Models/              # Models Eloquent
├── database/
│   ├── migrations/          # Migrations do banco
│   └── seeders/             # Seeders para dados iniciais
├── routes/
│   └── api.php              # Rotas da API
└── .env                     # Configurações de ambiente
```

## 🛠️ Comandos Úteis

```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear

# Ver todas as rotas
php artisan route:list

# Rollback de migrations
php artisan migrate:rollback

# Resetar banco de dados
php artisan migrate:fresh --seed
```
