# Sistema de Biblioteca

Projeto da disciplina de Desenvolvimento Web — UNIVASF / FACAPE (2026.1).
Equipe: Pedro Olavo Negreiro Rodrigues, Heloísa Cardoso Olimpio Barreto.

## Entrega Parcial 2 — Estrutura MVC e Rotas

Esta etapa entrega:
- Estrutura MVC completa (Model/View/Controller separados em `app/`);
- Controllers iniciais (`HomeController`, `LivroController`);
- Views iniciais, renderizadas dentro de um layout compartilhado;
- Sistema de rotas funcional, com suporte a parâmetros dinâmicos (`/livros/{id}`).

Os dados exibidos nesta etapa ainda são estáticos (mock). A conexão real
com o banco PostgreSQL via PDO será implementada na **Entrega Parcial 3**.

## Estrutura de pastas

```
library-manager/
├── public/              # Document root — único ponto de entrada HTTP
│   ├── index.php        # Front controller
│   └── .htaccess        # Rewrite para roteamento amigável
├── app/
│   ├── Core/
│   │   ├── Router.php       # Sistema de rotas
│   │   ├── Controller.php   # Controller base (método render/redirect)
│   │   └── Database.php     # Conexão PDO (será usada a partir da Entrega 3)
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   └── LivroController.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── home/index.php
│   │   ├── livros/{index,show,create}.php
│   │   └── errors/404.php
│   └── autoload.php     # Autoloader PSR-4 simples para o namespace App\
├── routes/
│   └── web.php          # Definição de todas as rotas
├── config/
│   └── config.php       # Configurações lidas de variáveis de ambiente
├── Dockerfile
├── docker-compose.yml
├── composer.json
└── .env.example
```

## Como executar (Docker)

```bash
docker compose up --build
```

A aplicação ficará disponível em **http://localhost:8080**.
O PostgreSQL ficará disponível em `localhost:5432` (usuário/senha em `.env.example`).

## Rotas disponíveis nesta etapa

| Método | Rota           | Controller@Action           | Descrição                          |
|--------|----------------|------------------------------|-------------------------------------|
| GET    | `/`            | HomeController@index         | Painel inicial                      |
| GET    | `/livros`      | LivroController@index        | Listagem de livros (dados mock)     |
| GET    | `/livros/novo` | LivroController@create        | Formulário de cadastro (visual)     |
| POST   | `/livros`      | LivroController@store         | Recebe o formulário (ainda não salva no banco) |
| GET    | `/livros/{id}` | LivroController@show          | Detalhes de um livro                |

## Próximas etapas

- **Entrega Parcial 3:** conexão com PostgreSQL via PDO, Create e Read reais.
- **Entrega Parcial 4:** CRUD completo (Update, Delete) e validações.
- **Entrega Parcial 5:** autenticação, sessões e perfis de acesso.
