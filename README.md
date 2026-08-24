# Sistema de Biblioteca

Projeto da disciplina de Desenvolvimento Web — UNIVASF (Análise e Desenvolvimento de Sistemas), 2026.1.
Equipe: Pedro Olavo Negreiro Rodrigues, Heloísa Cardoso Olimpio Barreto.

## Entrega Parcial 4 — CRUD Completo

Esta etapa completa o CRUD da entidade principal (Livro):
- **Create** e **Read**: implementados na Entrega Parcial 3;
- **Update**: edição de livros (`Livro::update()`), com formulário pré-preenchido
  e validação idêntica à do cadastro;
- **Delete**: exclusão de livros (`Livro::delete()`), com confirmação via JavaScript
  antes de excluir;
- Mensagens de sucesso e erro para todas as operações (criado / atualizado / excluído).

O formulário de cadastro e edição de livros foi unificado em uma única view
(`app/Views/livros/form.php`), seguindo o mesmo padrão já usado em Autor, Categoria
e Editora — reduzindo duplicação de código.

Também já estão prontos, desde ajustes anteriores:
- **CRUD completo de Autor** (`/autores`) — inclui nome e nacionalidade;
- **CRUD completo de Categoria** (`/categorias`);
- **CRUD completo de Editora** (`/editoras`).

> **Nota sobre o relacionamento Livro↔Autor:** o DER original (Entrega Parcial 1)
> previa relacionamento N:N via tabela associativa (`livro_autor`). A equipe optou
> por simplificar para N:1 (um autor por livro, selecionado via dropdown), decisão
> registrada em `database/init.sql`.

Testado localmente de ponta a ponta contra um PostgreSQL real antes desta entrega:
criar → editar (formulário pré-preenchido) → confirmar alteração na listagem e no
detalhe → validação bloqueando título/autor vazios → excluir → confirmar remoção.

## Estrutura de pastas

```
library-manager/
├── public/              # Document root — único ponto de entrada HTTP
│   ├── index.php        # Front controller
│   └── .htaccess        # Rewrite para roteamento amigável
├── app/
│   ├── Core/
│   │   ├── Router.php       # Sistema de rotas (GET/POST/PUT/DELETE, com _method override)
│   │   ├── Controller.php   # Controller base (método render/redirect)
│   │   └── Database.php     # Conexão PDO (Singleton)
│   ├── Models/
│   │   ├── Livro.php        # CRUD completo via PDO
│   │   ├── Autor.php        # CRUD completo
│   │   ├── Categoria.php    # CRUD completo
│   │   └── Editora.php      # CRUD completo
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── LivroController.php
│   │   ├── AutorController.php
│   │   ├── CategoriaController.php
│   │   └── EditoraController.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── home/index.php
│   │   ├── livros/{index,show,form}.php
│   │   ├── autores/{index,form}.php
│   │   ├── categorias/{index,form}.php
│   │   ├── editoras/{index,form}.php
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

> Se o volume do banco (`biblioteca_pgdata`) já existe de uma execução anterior e o
> schema mudou, o script de inicialização **não roda de novo** (o Postgres só executa
> `/docker-entrypoint-initdb.d` em bancos novos). Nesse caso, rode:
> ```bash
> docker compose down -v
> docker compose up --build
> ```
> **Atenção:** isso apaga os dados cadastrados no banco.
> Rode apenas `docker compose down` (sem `-v`) no dia a dia, para preservar os dados.

## Rotas disponíveis

| Método | Rota                  | Controller@Action        | Descrição                     |
|--------|-----------------------|---------------------------|--------------------------------|
| GET    | `/`                   | HomeController@index      | Painel inicial                 |
| GET    | `/livros`             | LivroController@index     | Listagem                       |
| GET    | `/livros/novo`        | LivroController@create    | Formulário de cadastro         |
| POST   | `/livros`             | LivroController@store     | Cadastra                       |
| GET    | `/livros/{id}`        | LivroController@show      | Detalhes                       |
| GET    | `/livros/{id}/editar` | LivroController@edit      | Formulário de edição           |
| PUT    | `/livros/{id}`        | LivroController@update    | Atualiza                       |
| DELETE | `/livros/{id}`        | LivroController@destroy   | Exclui                         |

O mesmo padrão (`index` / `novo` / `store` / `{id}/editar` / `update` / `destroy`)
se repete para `/autores`, `/categorias` e `/editoras`.

## Próximas etapas

- **Entrega Parcial 5:** autenticação, sessões, perfis de acesso e proteção de rotas.
