# Sistema de Biblioteca

Projeto da disciplina de Desenvolvimento Web — UNIVASF / FACAPE (2026.1).
Equipe: Pedro Olavo Negreiro Rodrigues, Heloísa Cardoso Olimpio Barreto.

## Entrega Parcial 3 — CRUD Inicial (Create + Read)

Esta etapa entrega:
- Conexão com o banco PostgreSQL via PDO (`App\Core\Database`, Singleton);
- Esquema completo do banco criado automaticamente (`database/init.sql`), refletindo o
  DER da Entrega Parcial 1, com dados iniciais (seed) de categorias, editoras e autores;
- **Create:** cadastro de livros (`app/Models/Livro.php::create()`), incluindo a
  associação N:N com autores (tabela `livro_autor`), dentro de uma transação;
- **Read:** listagem (`Livro::all()`) e detalhe (`Livro::find()`) de livros, com
  `JOIN`/`STRING_AGG` trazendo categoria, editora e autores já formatados;
- Validação server-side básica no cadastro (título obrigatório, ano válido, ao menos
  um autor selecionado), com mensagens de erro exibidas na própria tela do formulário;
- Painel inicial (dashboard) já consultando a quantidade real de livros cadastrados.

Testado localmente de ponta a ponta (schema → formulário vazio → cadastro válido →
listagem atualizada → contador do dashboard → cadastro inválido exibindo os 3 erros
esperados) antes desta entrega.

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
│   │   └── Database.php     # Conexão PDO (Singleton) — em uso desde a Entrega 3
│   ├── Models/
│   │   ├── Livro.php        # Create + Read (all/find) via PDO
│   │   ├── Categoria.php    # Lista de categorias (dropdown do formulário)
│   │   ├── Editora.php      # Lista de editoras (dropdown do formulário)
│   │   └── Autor.php        # Lista de autores (checkboxes do formulário)
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
├── database/
│   └── init.sql         # Schema completo (DER) + seed, executado no 1º boot do Postgres
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
Na primeira execução, o PostgreSQL cria automaticamente todas as tabelas e os dados
iniciais (categorias, editoras, autores) a partir de `database/init.sql`.

> Se você já tinha subido o projeto antes da Entrega 3 e o volume do banco (`biblioteca_pgdata`)
> já existe, o script de inicialização **não roda de novo** (o Postgres só executa
> `/docker-entrypoint-initdb.d` em bancos novos). Nesse caso, rode:
> ```bash
> docker compose down -v
> docker compose up --build
> ```
> para recriar o volume do zero com o schema atualizado.

## Rotas disponíveis nesta etapa

| Método | Rota           | Controller@Action           | Descrição                                   |
|--------|----------------|------------------------------|-----------------------------------------------|
| GET    | `/`            | HomeController@index         | Painel inicial (total de livros via banco)    |
| GET    | `/livros`      | LivroController@index        | Listagem real, via PDO                        |
| GET    | `/livros/novo` | LivroController@create        | Formulário de cadastro (dropdowns do banco)   |
| POST   | `/livros`      | LivroController@store         | Cadastra o livro no banco (com validação)     |
| GET    | `/livros/{id}` | LivroController@show          | Detalhes de um livro (via banco)              |

## Próximas etapas

- **Entrega Parcial 4:** Update e Delete de livros, mensagens de sucesso/erro completas.
- **Entrega Parcial 5:** autenticação, sessões e perfis de acesso.

