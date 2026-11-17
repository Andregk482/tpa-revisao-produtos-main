# Implementação da atividade — Loja de Produtos

Este projeto contém a implementação dos requisitos R1 a R8 e do bônus de estoque, mantendo as telas e os nomes de rotas/entidades fornecidos no projeto original.

## Funcionalidades implementadas

- **R1 — Listagem pública:** model `Produto`, migration `produtos`, relação `vendedor()` e listagem com `with('vendedor')`.
- **R2 — Login e logout:** autenticação com `Auth::attempt`, regeneração da sessão e encerramento seguro da sessão.
- **R3 — Middleware `auth`:** protege cadastro de produtos e as três rotas de compras.
- **R4 — Middleware `vendedor`:** alias registrado em `bootstrap/app.php`; clientes recebem HTTP 403 nas rotas de cadastro.
- **R5 — Cadastro validado:** `StoreProdutoRequest` valida todos os campos do contrato.
- **R6 — Dono do produto:** `vendedor_id` é preenchido no controller com o usuário autenticado, nunca pelo formulário.
- **R7 — Compra:** migrations de `vendas` e `produto_venda`, model `Venda` e registro das quantidades e preços unitários no momento da compra.
- **R8 — Minhas compras:** consulta somente as vendas do cliente autenticado e carrega os produtos associados.
- **Bônus — Estoque:** confere todo o estoque antes de criar a venda e baixa as quantidades dentro de uma transação.

## Como executar

Na pasta do projeto:

```bash
composer install
cp .env.example .env
php artisan key:generate
touch database/database.sqlite
php artisan migrate:fresh --seed
php artisan serve
```

Abra `http://localhost:8000/produtos`.

## Usuários para conferência

Todos usam a senha `senha123`:

| E-mail | Perfil |
| --- | --- |
| ana@loja.test | vendedor |
| bruno@loja.test | vendedor |
| carla@loja.test | cliente |
| diego@loja.test | cliente |

## Observação de validação

Os arquivos PHP alterados e adicionados foram verificados com `php -l` (sem erros de sintaxe). O ambiente em que o pacote foi preparado não tinha as dependências Composer instaladas e não conseguiu acessar a Internet para baixá-las; por isso, os comandos Artisan e os testes de integração precisam ser executados localmente após `composer install`.
