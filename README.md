# Sistema de Biblioteca

Documentação completa do sistema desenvolvido para a disciplina de
**Projeto e Implementação de Sistemas para Web 2** — UNIVASF (Fundação
Universidade Federal do Vale do São Francisco), curso de Análise e
Desenvolvimento de Sistemas.

## Equipe

| Nome | GitHub |
|---|---|
| Pedro Olavo Negreiro Rodrigues | [@pedro-olavo](https://github.com/pedro-olavo) |
| Heloísa Cardoso Olimpio Barreto | — |

**Repositório:** https://github.com/pedro-olavo/library-manager

---

## Sumário

1. [Visão geral](#visão-geral)
2. [Tecnologias utilizadas](#tecnologias-utilizadas)
3. [Arquitetura](#arquitetura)
4. [Modelo de dados](#modelo-de-dados)
5. [Módulos do sistema](#módulos-do-sistema)
6. [Perfis de usuário e controle de acesso](#perfis-de-usuário-e-controle-de-acesso)
7. [Estrutura de pastas](#estrutura-de-pastas)
8. [Rotas da aplicação](#rotas-da-aplicação)
9. [Como executar localmente](#como-executar-localmente)
10. [Deploy em produção](#deploy-em-produção)
11. [Usuários e dados de demonstração](#usuários-e-dados-de-demonstração)
12. [Decisões de modelagem e histórico do projeto](#decisões-de-modelagem-e-histórico-do-projeto)
13. [Limitações conhecidas e próximos passos](#limitações-conhecidas-e-próximos-passos)

---

## Visão geral

O Sistema de Biblioteca é uma aplicação web para gerenciamento completo do
acervo, dos usuários e das operações de empréstimo de uma biblioteca. Ele
cobre o ciclo de uso real de uma biblioteca de pequeno/médio porte:

- Cadastro do acervo (livros, autores, categorias, editoras);
- Controle de exemplares físicos de cada livro (múltiplas cópias, cada uma
  com seu próprio status);
- Autenticação de usuários com três perfis de acesso distintos;
- Registro de empréstimos e devoluções, com detecção automática de atraso;
- Painel inicial com indicadores gerais do sistema.

O projeto foi desenvolvido de forma incremental ao longo do semestre,
seguindo o cronograma de entregas parciais da disciplina, e evoluiu para
incluir um módulo de empréstimos completo antes da entrega final.

## Tecnologias utilizadas

| Camada | Tecnologia |
|---|---|
| Linguagem | PHP 8.2 |
| Arquitetura | MVC (Model-View-Controller), implementada manualmente (sem framework) |
| Banco de dados | PostgreSQL 16, acessado via PDO |
| Servidor web | Apache 2.4 (imagem `php:8.2-apache`) |
| Containerização | Docker e Docker Compose |
| Controle de versão | Git / GitHub |
| Autenticação | Sessões PHP nativas + `password_hash()`/`password_verify()` (bcrypt) |
| Deploy | Railway (build a partir do `Dockerfile`, PostgreSQL gerenciado) |

## Arquitetura

A aplicação segue o padrão **MVC** com um roteador e autoloader próprios
(sem uso de framework), estruturados da seguinte forma:

- **Front Controller** (`public/index.php`): ponto de entrada único de toda
  requisição HTTP. Inicia a sessão (`App\Core\Auth::start()`), instancia o
  `Router` e despacha a requisição.
- **Router** (`app/Core/Router.php`): sistema de rotas baseado em expressões
  regulares, com suporte a `GET`, `POST`, `PUT` e `DELETE` (as duas últimas
  simuladas via campo oculto `_method` em formulários HTML, já que browsers
  não enviam PUT/DELETE nativamente) e a parâmetros dinâmicos na URL
  (ex: `/livros/{id}`).
- **Controllers** (`app/Controllers/`): recebem a requisição já roteada,
  aplicam validação e regras de controle de acesso, e delegam a lógica de
  dados aos Models.
- **Models** (`app/Models/`): toda a comunicação com o banco de dados,
  usando PDO com *prepared statements* (proteção contra SQL Injection).
- **Views** (`app/Views/`): templates PHP puro, renderizados dentro de um
  layout compartilhado (`app/Views/layout.php`) que já contempla o menu de
  navegação adaptado ao perfil do usuário logado.
- **Core** (`app/Core/`): classes de infraestrutura compartilhada —
  `Router`, `Controller` (classe base com `render()`/`redirect()`),
  `Database` (conexão PDO em Singleton) e `Auth` (sessão e controle de acesso).

Não há uso de Composer para autoload em produção: um autoloader PSR-4
simples (`app/autoload.php`) mapeia o namespace `App\` para a pasta `app/`,
eliminando a necessidade de `composer install` no ambiente de deploy.

## Modelo de dados

O banco de dados é PostgreSQL, com o esquema completo definido em
`database/init.sql` — executado automaticamente pelo container do Postgres
na primeira inicialização (mecanismo `docker-entrypoint-initdb.d`).

### Tabelas

| Tabela | Descrição |
|---|---|
| `usuario` | Contas do sistema — nome, e-mail, senha (hash bcrypt) e perfil (`administrador`, `bibliotecario` ou `leitor`) |
| `autor` | Autores dos livros (nome, nacionalidade) |
| `editora` | Editoras (nome) |
| `categoria` | Categorias temáticas dos livros (nome) |
| `livro` | Livros cadastrados — título, ISBN, ano, e referências para autor, editora e categoria |
| `exemplar` | Cópias físicas de um livro — código de patrimônio e status (`disponivel`, `emprestado`, `reservado`, `manutencao`) |
| `emprestimo` | Registro de empréstimos — usuário, exemplar, datas e status (`em_dia`, `atrasado`, `devolvido`) |
| `reserva` | Estrutura já modelada para reservas futuras (ver [Limitações](#limitações-conhecidas-e-próximos-passos)) |

### Relacionamentos principais

```
usuario 1───N emprestimo N───1 exemplar N───1 livro N───1 autor
                                              livro N───1 editora
                                              livro N───1 categoria
```

- Um **livro** pertence a um autor, uma editora e uma categoria (relação N:1
  para cada — ver nota sobre a simplificação do relacionamento com autor em
  [Decisões de modelagem](#decisões-de-modelagem-e-histórico-do-projeto));
- Um **livro** pode ter vários **exemplares** (cópias físicas individuais);
- Um **empréstimo** está sempre associado a um exemplar específico (não ao
  livro diretamente) e a um usuário;
- Exclusão de autor/editora/categoria usa `ON DELETE SET NULL` no livro
  (não quebra o cadastro do livro se a referência for removida); exclusão de
  livro usa `ON DELETE CASCADE` nos seus exemplares; exclusão de exemplar
  com empréstimos associados é bloqueada pela chave estrangeira (sem
  CASCADE), preservando o histórico.

## Módulos do sistema

### 1. Autenticação e controle de acesso

Login e logout via sessão PHP nativa (`App\Core\Auth`). Senhas nunca são
armazenadas em texto puro — usa-se `password_hash()` (bcrypt) no cadastro e
`password_verify()` na autenticação. Cada controller declara, no construtor,
quais perfis podem acessar suas ações (`Auth::requireLogin()` /
`Auth::requireRole([...])`); acesso negado retorna HTTP 403.

**Arquivos:** `app/Core/Auth.php`, `app/Controllers/AuthController.php`,
`app/Views/auth/login.php`.

### 2. Gestão de usuários (CRUD)

Restrito ao perfil **administrador**. Permite cadastrar, editar, excluir e
atribuir perfis de acesso. A senha é opcional na edição (deixar em branco
mantém a senha atual). Um administrador não pode excluir a própria conta
enquanto está logado (proteção explícita no controller).

**Arquivos:** `app/Models/Usuario.php`, `app/Controllers/UsuarioController.php`,
`app/Views/usuarios/`.

### 3. Gestão de livros (CRUD completo)

Entidade principal do sistema. Cadastro com título, ISBN, ano de publicação,
autor, categoria e editora (os três últimos via dropdown, alimentados pelos
respectivos módulos de cadastro). Leitura, atualização e exclusão completas,
com validação server-side (título obrigatório, ano numérico, autor
obrigatório).

**Controle de acesso:** qualquer usuário autenticado pode listar e ver
detalhes; cadastrar, editar e excluir exige perfil administrador ou
bibliotecário.

**Arquivos:** `app/Models/Livro.php`, `app/Controllers/LivroController.php`,
`app/Views/livros/`.

### 4. Gestão de autores, categorias e editoras (CRUDs de apoio)

Três módulos estruturalmente idênticos — listagem, cadastro, edição e
exclusão — que alimentam os dropdowns do cadastro de livros. Restritos a
administrador/bibliotecário.

**Arquivos:** `app/Models/{Autor,Categoria,Editora}.php`,
`app/Controllers/{Autor,Categoria,Editora}Controller.php`,
`app/Views/{autores,categorias,editoras}/`.

### 5. Gestão de exemplares

Cada livro pode ter várias cópias físicas (exemplares), cada uma com um
código de patrimônio único e um status. Gerenciado diretamente na tela de
detalhes do livro (não tem rota/tela própria de listagem geral). A exclusão
de um exemplar com histórico de empréstimo é bloqueada pelo banco (chave
estrangeira) e tratada com uma mensagem amigável na interface.

**Arquivos:** `app/Models/Exemplar.php`, `app/Controllers/ExemplarController.php`
(ações incorporadas em `app/Views/livros/show.php`).

### 6. Módulo de empréstimos

O módulo mais elaborado do sistema, cobrindo o ciclo completo de uso:

- **Registrar empréstimo:** o bibliotecário/administrador escolhe o
  **usuário** e o **livro** desejado — não o exemplar específico. O
  dropdown de livros mostra apenas os que têm ao menos uma cópia disponível,
  com a contagem (ex.: "Dom Casmurro (2 disponíveis)"). O sistema escolhe
  automaticamente qual exemplar sai, dentro de uma transação com
  `SELECT ... FOR UPDATE` (trava de linha) para evitar que dois empréstimos
  peguem o mesmo exemplar simultaneamente;
- **Atraso automático:** a cada listagem, `Emprestimo::markOverdue()` roda
  um `UPDATE` que marca como `atrasado` todo empréstimo cuja data prevista
  de devolução já passou — sem depender de um job agendado;
- **Registrar devolução:** marca a data de devolução e libera o exemplar
  (volta a `disponivel`) automaticamente, também em transação;
- **Visibilidade por perfil:** leitores veem apenas os próprios empréstimos;
  administrador/bibliotecário veem e gerenciam todos. Registrar empréstimo e
  devolução exige perfil administrador ou bibliotecário;
- **Filtro** por empréstimos "Ativos" ou "Todos" na listagem.

O painel inicial (`/`) mostra a contagem real de livros cadastrados e de
empréstimos ativos, ambos consultados diretamente do banco.

**Arquivos:** `app/Models/Emprestimo.php`, `app/Controllers/EmprestimoController.php`,
`app/Views/emprestimos/`.

## Perfis de usuário e controle de acesso

| Ação | Leitor | Bibliotecário | Administrador |
|---|:---:|:---:|:---:|
| Ver acervo (livros) | ✅ | ✅ | ✅ |
| Ver os próprios empréstimos | ✅ | ✅ | ✅ |
| Ver e gerenciar todos os empréstimos | ❌ | ✅ | ✅ |
| Cadastrar/editar/excluir livros | ❌ | ✅ | ✅ |
| Gerenciar autores, categorias, editoras | ❌ | ✅ | ✅ |
| Registrar empréstimo/devolução | ❌ | ✅ | ✅ |
| Gerenciar usuários e perfis | ❌ | ❌ | ✅ |

Toda a aplicação exige login, exceto a própria tela de `/login`. Tentativas
de acesso sem permissão retornam HTTP 403 com uma página dedicada.

## Estrutura de pastas

```
library-manager/
├── public/                    # Document root — único ponto de entrada HTTP
│   ├── index.php              # Front controller (inicia sessão, roteia)
│   └── .htaccess              # Rewrite para roteamento amigável
├── app/
│   ├── Core/
│   │   ├── Router.php         # Sistema de rotas
│   │   ├── Controller.php     # Controller base (render/redirect)
│   │   ├── Database.php       # Conexão PDO (Singleton)
│   │   └── Auth.php           # Sessão, login/logout, requireLogin/requireRole
│   ├── Models/
│   │   ├── Livro.php
│   │   ├── Autor.php
│   │   ├── Categoria.php
│   │   ├── Editora.php
│   │   ├── Usuario.php
│   │   ├── Exemplar.php
│   │   └── Emprestimo.php
│   ├── Controllers/
│   │   ├── HomeController.php
│   │   ├── AuthController.php
│   │   ├── LivroController.php
│   │   ├── AutorController.php
│   │   ├── CategoriaController.php
│   │   ├── EditoraController.php
│   │   ├── UsuarioController.php
│   │   ├── ExemplarController.php
│   │   └── EmprestimoController.php
│   ├── Views/
│   │   ├── layout.php
│   │   ├── auth/login.php
│   │   ├── home/index.php
│   │   ├── livros/{index,show,form}.php
│   │   ├── autores/{index,form}.php
│   │   ├── categorias/{index,form}.php
│   │   ├── editoras/{index,form}.php
│   │   ├── usuarios/{index,form}.php
│   │   ├── emprestimos/{index,form}.php
│   │   └── errors/{404,403}.php
│   └── autoload.php           # Autoloader PSR-4 simples para o namespace App\
├── routes/
│   └── web.php                # Definição de todas as rotas
├── config/
│   └── config.php             # Configurações lidas de variáveis de ambiente
├── database/
│   └── init.sql               # Schema completo + dados de demonstração
├── Dockerfile
├── docker-compose.yml
├── docker-entrypoint.sh       # Entrypoint do container (ver nota abaixo)
├── composer.json
└── .env.example
```

## Rotas da aplicação

| Método | Rota | Controller@Action | Acesso |
|---|---|---|---|
| GET | `/login` | AuthController@showLogin | Público |
| POST | `/login` | AuthController@login | Público |
| POST | `/logout` | AuthController@logout | Autenticado |
| GET | `/` | HomeController@index | Autenticado |
| GET | `/livros` | LivroController@index | Autenticado |
| GET | `/livros/{id}` | LivroController@show | Autenticado |
| GET | `/livros/novo` | LivroController@create | Administrador / Bibliotecário |
| POST | `/livros` | LivroController@store | Administrador / Bibliotecário |
| GET | `/livros/{id}/editar` | LivroController@edit | Administrador / Bibliotecário |
| PUT | `/livros/{id}` | LivroController@update | Administrador / Bibliotecário |
| DELETE | `/livros/{id}` | LivroController@destroy | Administrador / Bibliotecário |
| POST | `/livros/{id}/exemplares` | ExemplarController@store | Administrador / Bibliotecário |
| DELETE | `/exemplares/{id}` | ExemplarController@destroy | Administrador / Bibliotecário |
| GET | `/emprestimos` | EmprestimoController@index | Autenticado (leitor vê só os próprios) |
| GET | `/emprestimos/novo` | EmprestimoController@create | Administrador / Bibliotecário |
| POST | `/emprestimos` | EmprestimoController@store | Administrador / Bibliotecário |
| POST | `/emprestimos/{id}/devolver` | EmprestimoController@devolver | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/autores/...` | AutorController | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/categorias/...` | CategoriaController | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/editoras/...` | EditoraController | Administrador / Bibliotecário |
| GET/POST/PUT/DELETE | `/usuarios/...` | UsuarioController | Administrador |

## Como executar localmente

Pré-requisito: Docker e Docker Compose instalados.

```bash
docker compose up --build
```

A aplicação sobe em **http://localhost:8080**. Na primeira execução, o
PostgreSQL cria automaticamente todas as tabelas e os dados de demonstração
(seção seguinte) a partir de `database/init.sql`.

> **Atenção:** se o volume do banco (`biblioteca_pgdata`) já existir de uma
> execução anterior e o schema tiver mudado, o script de inicialização **não
> roda de novo** (o Postgres só executa `/docker-entrypoint-initdb.d` em
> bancos novos). Nesse caso:
> ```bash
> docker compose down -v   # remove containers e o volume do banco
> docker compose up --build
> ```
> Isso apaga todos os dados cadastrados. No dia a dia, use apenas
> `docker compose down` (sem `-v`) para preservar os dados entre reinícios.

## Deploy em produção

A aplicação está preparada para deploy via **Railway** a partir do
`Dockerfile` (também funciona em qualquer plataforma que suporte imagens
Docker, como Render ou um VPS próprio).

### Passo a passo (Railway)

1. Criar um novo projeto a partir do repositório GitHub — a Railway detecta
   o `Dockerfile` automaticamente (confirme em *Settings → Build → Builder*
   que está definido como **Dockerfile**, e não Nixpacks);
2. Adicionar um serviço **PostgreSQL** gerenciado ao mesmo projeto
   (botão `+ New` → `Database` → `Add PostgreSQL`);
3. No serviço da aplicação, configurar as variáveis de ambiente **referenciando**
   o serviço de banco (não copiar valores fixos do `.env.example`):
   ```
   DB_HOST     = ${{Postgres.PGHOST}}
   DB_PORT     = ${{Postgres.PGPORT}}
   DB_DATABASE = ${{Postgres.PGDATABASE}}
   DB_USERNAME = ${{Postgres.PGUSER}}
   DB_PASSWORD = ${{Postgres.PGPASSWORD}}
   ```
   (o nome exato do serviço entre `${{ }}` depende de como ele foi nomeado
   no seu projeto — a Railway sugere o autocomplete ao digitar `${{`);
4. Rodar o `database/init.sql` manualmente no banco gerenciado (bancos
   gerenciados **não** executam scripts de inicialização como o Postgres
   local do `docker-compose` faz). Duas formas:
   - Pela aba **Data**/**Query** do próprio serviço Postgres na Railway
     (cole o conteúdo do arquivo e execute); ou
   - Localmente, com `psql "<connection-string-da-aba-Connect>" -f database/init.sql`;
5. Gerar o domínio público em *Settings → Networking → Generate Domain*.

### Nota técnica: `docker-entrypoint.sh`

A imagem base `php:8.2-apache` pode, em determinados ambientes de build,
subir com mais de um módulo MPM do Apache habilitado simultaneamente
(`mpm_prefork` + `mpm_event`), o que impede o Apache de iniciar com o erro
`AH00534: More than one MPM loaded`. O `Dockerfile` já corrige isso no
build (`a2dismod`/`a2enmod`), e o `docker-entrypoint.sh` **reaplica essa
correção a cada início do container** como camada extra de segurança, já
que esse problema se mostrou, em alguns cenários de deploy, não resolvido
apenas pela correção em tempo de build.

## Usuários e dados de demonstração

O `database/init.sql` já cria, na primeira inicialização, três contas (uma
por perfil) e dois livros de exemplo com exemplares disponíveis — o sistema
fica funcional imediatamente, sem exigir cadastro manual antes da primeira
demonstração:

| Perfil | E-mail | Senha |
|---|---|---|
| Administrador | administrador@biblioteca.com | admin123 |
| Bibliotecário | bibliotecario@biblioteca.com | biblio123 |
| Leitor | leitor@biblioteca.com | leitor123 |

| Livro | Exemplares |
|---|---|
| Dom Casmurro (Machado de Assis) | BIB-0001, BIB-0002 |
| 1984 (George Orwell) | BIB-0003 |

## Decisões de modelagem e histórico do projeto

O projeto evoluiu de forma incremental; algumas decisões de modelagem
tomadas ao longo do caminho valem registro para contextualizar diferenças
em relação ao planejamento inicial:

- **Relacionamento Livro↔Autor:** o DER original (planejamento inicial)
  previa relacionamento N:N entre livro e autor, via uma tabela associativa
  (`livro_autor`). A equipe optou por simplificar para N:1 (um autor
  principal por livro, selecionado via dropdown simples no cadastro), o que
  está refletido na coluna `autor_id` diretamente na tabela `livro`. Essa
  simplificação foi decidida durante a implementação do CRUD de livros e
  pode ser revertida no futuro caso o cadastro de livros com múltiplos
  autores se mostre necessário.
- **Seleção de exemplar no empréstimo:** inicialmente, o formulário de
  registro de empréstimo pedia a escolha manual do exemplar específico
  (por código de patrimônio). Isso foi simplificado para que o bibliotecário
  escolha apenas o **livro**, com o sistema selecionando automaticamente uma
  cópia disponível — reduz a chance de erro operacional e reflete melhor
  como uma biblioteca real funciona (o usuário não se importa com qual
  cópia física recebe).

## Limitações conhecidas e próximos passos

- **Reservas:** a tabela `reserva` já está modelada no schema, mas ainda não
  possui Model, Controller ou views associadas — um leitor não pode
  reservar um livro indisponível no momento;
- **Relatórios/indicadores:** o painel inicial mostra apenas contagens
  simples (total de livros, empréstimos ativos); não há relatórios
  históricos ou exportação de dados;
- **Recuperação de senha:** não há fluxo de "esqueci minha senha" — a
  redefinição só pode ser feita por um administrador, editando o usuário.
