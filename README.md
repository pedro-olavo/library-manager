# Sistema de Biblioteca

Projeto da disciplina de Desenvolvimento Web — UNIVASF (Análise e Desenvolvimento de Sistemas), 2026.1.
Equipe: Pedro Olavo Negreiro Rodrigues, Heloísa Cardoso Olimpio Barreto.

## Módulo de Empréstimos (preparação para o Projeto Final)

Construído após a Entrega Parcial 5, antes da entrega final, para que o sistema
cubra o ciclo completo de uso de uma biblioteca — não só cadastro de acervo, mas
o empréstimo em si.

- **Exemplares**: cada livro pode ter vários exemplares (cópias físicas), cada
  um com código de patrimônio e status (`disponivel` / `emprestado` / `reservado`
  / `manutencao`). Gerenciados diretamente na tela de detalhes do livro
  (`app/Controllers/ExemplarController.php`);
- **Empréstimos** (`app/Controllers/EmprestimoController.php`,
  `app/Models/Emprestimo.php`):
  - Registrar empréstimo: seleciona usuário + **livro** (não o exemplar
    específico) + data prevista de devolução. O dropdown de livros mostra
    apenas os que têm ao menos 1 exemplar disponível, com a contagem
    (ex: "Dom Casmurro (2 disponíveis)") — o sistema escolhe automaticamente
    qual cópia física sai, usando transação com `SELECT ... FOR UPDATE` para
    evitar que dois empréstimos peguem o mesmo exemplar ao mesmo tempo;
  - Registrar devolução: marca a data de devolução e libera o exemplar
    automaticamente;
  - Atraso é recalculado a cada listagem (`Emprestimo::markOverdue()`), sem
    depender de um job agendado;
  - **Controle de acesso**: leitores veem apenas os próprios empréstimos;
    administrador/bibliotecário veem e gerenciam todos; registrar empréstimo
    e devolução exige perfil administrador ou bibliotecário;
- O painel inicial agora mostra a contagem real de empréstimos ativos
  (antes era um valor fixo em zero);
- **Dados de exemplo já no seed** (`database/init.sql`): os livros "Dom
  Casmurro" (2 exemplares) e "1984" (1 exemplar) já vêm cadastrados com
  cópias disponíveis, para que o módulo de empréstimos funcione imediatamente
  após o primeiro `docker compose up`, sem precisar cadastrar nada manualmente.

Testado de ponta a ponta contra um PostgreSQL real: formulário mostra os livros
com contagem de exemplares disponíveis (sem expor o exemplar/código individual)
→ registro de empréstimo escolhe a cópia automaticamente → exemplar muda para
"emprestado" → simulação de atraso → devolução → exemplar volta a "disponível"
→ contadores do dashboard corretos.

## Entrega Parcial 5 — Autenticação e Controle de Acesso

Esta etapa entrega:
- **Login/Logout** via sessão PHP (`App\Core\Auth`, iniciada em `public/index.php`);
- **Armazenamento seguro de senhas**: `password_hash()` (bcrypt) no cadastro,
  `password_verify()` na autenticação — a senha em texto puro nunca é gravada
  nem fica acessível depois do hash;
- **Perfis de usuário**: `administrador`, `bibliotecario` e `leitor`, com um
  CRUD completo de usuários (`/usuarios`, restrito a administradores) para
  cadastrar, editar e excluir contas e atribuir perfis;
- **Proteção de rotas**:
  - Todo o sistema exige login (`Auth::requireLogin()`), exceto `/login`;
  - Cadastro/edição/exclusão de livros, autores, categorias e editoras exige
    perfil `administrador` ou `bibliotecario` (`Auth::requireRole()`);
    leitores só podem consultar o acervo (listagem e detalhe);
  - Gestão de usuários exige perfil `administrador`;
  - Acesso negado retorna HTTP 403 (`app/Views/errors/403.php`);
- A interface (menu, botões de ação) se adapta ao perfil logado — leitores não
  veem os botões "Novo Livro", "Editar" ou "Excluir", por exemplo.

**Usuários de demonstração** (criados automaticamente pelo `database/init.sql`):

| Perfil         | E-mail                        | Senha       |
|----------------|--------------------------------|-------------|
| Administrador  | administrador@biblioteca.com   | admin123    |
| Bibliotecário  | bibliotecario@biblioteca.com   | biblio123   |
| Leitor         | leitor@biblioteca.com          | leitor123   |

Testado localmente de ponta a ponta contra um PostgreSQL real antes desta entrega:
acesso sem login redireciona para `/login`; leitor recebe 403 ao tentar cadastrar
livro ou acessar `/usuarios`; bibliotecário gerencia o acervo mas não usuários;
administrador cria usuários e **não consegue excluir a própria conta** (proteção
testada e confirmada no banco).

## Estrutura de pastas

```
library-manager/
├── public/              # Document root — único ponto de entrada HTTP
│   ├── index.php        # Front controller (inicia sessão, roteia)
│   └── .htaccess        # Rewrite para roteamento amigável
├── app/
│   ├── Core/
│   │   ├── Router.php       # Sistema de rotas (GET/POST/PUT/DELETE, _method override)
│   │   ├── Controller.php   # Controller base (render/redirect)
│   │   ├── Database.php     # Conexão PDO (Singleton)
│   │   └── Auth.php         # Sessão, login/logout, requireLogin/requireRole
│   ├── Models/
│   │   ├── Livro.php        # CRUD completo
│   │   ├── Autor.php        # CRUD completo
│   │   ├── Categoria.php    # CRUD completo
│   │   ├── Editora.php      # CRUD completo
│   │   ├── Usuario.php      # CRUD completo + authenticate() (bcrypt)
│   │   ├── Exemplar.php     # CRUD de cópias físicas de um livro
│   │   └── Emprestimo.php   # create/registrarDevolucao/markOverdue
│   ├── Controllers/
│   │   ├── HomeController.php    # exige login
│   │   ├── LivroController.php   # exige login; escrita exige admin/bibliotecário
│   │   ├── AutorController.php   # exige admin/bibliotecário
│   │   ├── CategoriaController.php
│   │   ├── EditoraController.php
│   │   ├── AuthController.php    # login/logout (público)
│   │   ├── UsuarioController.php # exige administrador
│   │   ├── ExemplarController.php   # exige admin/bibliotecário
│   │   └── EmprestimoController.php # exige login; registrar exige admin/bibliotecário
│   ├── Views/
│   │   ├── layout.php       # menu adaptado ao perfil, usuário logado, botão sair
│   │   ├── auth/login.php
│   │   ├── home/index.php
│   │   ├── livros/{index,show,form}.php
│   │   ├── autores/{index,form}.php
│   │   ├── categorias/{index,form}.php
│   │   ├── editoras/{index,form}.php
│   │   ├── usuarios/{index,form}.php
│   │   ├── emprestimos/{index,form}.php
│   │   └── errors/{404,403}.php
│   └── autoload.php     # Autoloader PSR-4 simples para o namespace App\
├── routes/
│   └── web.php          # Definição de todas as rotas
├── config/
│   └── config.php       # Configurações lidas de variáveis de ambiente
├── database/
│   └── init.sql         # Schema completo (DER) + seed (categorias, editoras, autores, usuários)
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
iniciais (categorias, editoras, autores e os 3 usuários de demonstração acima)
a partir de `database/init.sql`.

> Se o volume do banco (`biblioteca_pgdata`) já existe de uma execução anterior e o
> schema mudou, o script de inicialização **não roda de novo**. Nesse caso, rode:
> ```bash
> docker compose down -v
> docker compose up --build
> ```
> **Atenção:** isso apaga os dados cadastrados no banco (inclusive os usuários
> de demonstração, que serão recriados). Rode apenas `docker compose down`
> (sem `-v`) no dia a dia, para preservar os dados.

## Rotas disponíveis

| Método | Rota                  | Controller@Action        | Acesso                          |
|--------|-----------------------|---------------------------|----------------------------------|
| GET    | `/login`              | AuthController@showLogin  | Público                          |
| POST   | `/login`               | AuthController@login      | Público                          |
| POST   | `/logout`              | AuthController@logout     | Autenticado                      |
| GET    | `/`                   | HomeController@index      | Autenticado                      |
| GET    | `/livros`             | LivroController@index     | Autenticado                      |
| GET    | `/livros/{id}`        | LivroController@show      | Autenticado                      |
| GET/POST/PUT/DELETE | `/livros/...` (cadastro, edição, exclusão) | LivroController | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/autores/...`, `/categorias/...`, `/editoras/...` | — | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/usuarios/...` | UsuarioController | Administrador |
| POST   | `/livros/{id}/exemplares` | ExemplarController@store   | Administrador / Bibliotecário |
| DELETE | `/exemplares/{id}`        | ExemplarController@destroy | Administrador / Bibliotecário |
| GET    | `/emprestimos`            | EmprestimoController@index | Autenticado (leitor vê só os próprios) |
| GET/POST | `/emprestimos/novo`, `/emprestimos` | EmprestimoController@create/store | Administrador / Bibliotecário |
| POST   | `/emprestimos/{id}/devolver` | EmprestimoController@devolver | Administrador / Bibliotecário |

## Próximas etapas

- Reservas (a tabela `reserva` já existe no schema, mas ainda não tem CRUD/telas
  associadas — um leitor não pode reservar um livro indisponível ainda);
- Deploy da aplicação e documentação final (Projeto Final).
