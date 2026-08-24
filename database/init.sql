-- ============================================================
-- Sistema de Biblioteca - Esquema do banco de dados (PostgreSQL)
-- Baseado no DER/MER apresentado na Entrega Parcial 1.
--
-- Este script é executado automaticamente pelo container do
-- PostgreSQL na primeira inicialização (ver docker-compose.yml,
-- volume mapeado para /docker-entrypoint-initdb.d/).
-- ============================================================

CREATE TABLE IF NOT EXISTS usuario (
    id            SERIAL PRIMARY KEY,
    nome          VARCHAR(150) NOT NULL,
    email         VARCHAR(150) NOT NULL UNIQUE,
    senha_hash    VARCHAR(255) NOT NULL,
    perfil        VARCHAR(20)  NOT NULL DEFAULT 'leitor', -- administrador | bibliotecario | leitor
    data_cadastro TIMESTAMP NOT NULL DEFAULT NOW()
);

CREATE TABLE IF NOT EXISTS autor (
    id            SERIAL PRIMARY KEY,
    nome          VARCHAR(150) NOT NULL,
    nacionalidade VARCHAR(80)
);

CREATE TABLE IF NOT EXISTS editora (
    id   SERIAL PRIMARY KEY,
    nome VARCHAR(150) NOT NULL
);

CREATE TABLE IF NOT EXISTS categoria (
    id   SERIAL PRIMARY KEY,
    nome VARCHAR(80) NOT NULL
);

CREATE TABLE IF NOT EXISTS livro (
    id              SERIAL PRIMARY KEY,
    titulo          VARCHAR(200) NOT NULL,
    isbn            VARCHAR(20),
    ano_publicacao  INTEGER,
    autor_id        INTEGER REFERENCES autor(id) ON DELETE SET NULL,
    editora_id      INTEGER REFERENCES editora(id) ON DELETE SET NULL,
    categoria_id    INTEGER REFERENCES categoria(id) ON DELETE SET NULL,
    criado_em       TIMESTAMP NOT NULL DEFAULT NOW()
);

-- OBS: o DER original (Entrega Parcial 1) previa relacionamento N:N entre
-- Livro e Autor via uma tabela associativa (livro_autor). A equipe optou por
-- simplificar para um relacionamento N:1 (um autor principal por livro),
-- refletido acima pela coluna autor_id em livro. Essa é uma decisão de
-- modelagem tomada nesta etapa (Entrega Parcial 3) e pode ser revista
-- futuramente caso o cadastro de livros com múltiplos autores se mostre
-- necessário.

CREATE TABLE IF NOT EXISTS exemplar (
    id                SERIAL PRIMARY KEY,
    livro_id          INTEGER NOT NULL REFERENCES livro(id) ON DELETE CASCADE,
    codigo_patrimonio VARCHAR(50) UNIQUE,
    status            VARCHAR(20) NOT NULL DEFAULT 'disponivel' -- disponivel | emprestado | reservado | manutencao
);

CREATE TABLE IF NOT EXISTS emprestimo (
    id                       SERIAL PRIMARY KEY,
    usuario_id               INTEGER NOT NULL REFERENCES usuario(id),
    exemplar_id              INTEGER NOT NULL REFERENCES exemplar(id),
    data_emprestimo          DATE NOT NULL DEFAULT CURRENT_DATE,
    data_prevista_devolucao  DATE NOT NULL,
    data_devolucao           DATE,
    status                   VARCHAR(20) NOT NULL DEFAULT 'em_dia' -- em_dia | atrasado | devolvido
);

CREATE TABLE IF NOT EXISTS reserva (
    id           SERIAL PRIMARY KEY,
    usuario_id   INTEGER NOT NULL REFERENCES usuario(id),
    livro_id     INTEGER NOT NULL REFERENCES livro(id),
    data_reserva DATE NOT NULL DEFAULT CURRENT_DATE,
    status       VARCHAR(20) NOT NULL DEFAULT 'ativa' -- ativa | cancelada | concluida
);

-- ============================================================
-- Dados iniciais (seed) para permitir a demonstração funcional
-- do sistema já nesta etapa (Entrega Parcial 3).
-- ============================================================

INSERT INTO categoria (nome) VALUES
    ('Romance'), ('Ficção'), ('Tecnologia'), ('História'), ('Infantil')
ON CONFLICT DO NOTHING;

INSERT INTO editora (nome) VALUES
    ('Companhia das Letras'), ('Editora Globo'), ('O''Reilly Media'), ('Ática'), ('Record')
ON CONFLICT DO NOTHING;

INSERT INTO autor (nome, nacionalidade) VALUES
    ('Machado de Assis', 'Brasileira'),
    ('George Orwell', 'Britânica'),
    ('Robert C. Martin', 'Americana'),
    ('Aluísio Azevedo', 'Brasileira'),
    ('Clarice Lispector', 'Brasileira')
ON CONFLICT DO NOTHING;

-- Usuários de demonstração (Entrega Parcial 5).
-- Senhas em texto puro, apenas para referência da equipe durante o desenvolvimento
-- e demonstração — NUNCA são armazenadas assim no banco (ver senha_hash abaixo,
-- gerado com password_hash()/PASSWORD_BCRYPT em PHP):
--   administrador@biblioteca.com / admin123
--   bibliotecario@biblioteca.com / biblio123
--   leitor@biblioteca.com        / leitor123
INSERT INTO usuario (nome, email, senha_hash, perfil) VALUES
    ('Administrador do Sistema', 'administrador@biblioteca.com', '$2y$10$VkLxDUa2/0Pk1JxpKSrJWem8B/hdMLhJkPniezsIKqhuoHDX5jpCm', 'administrador'),
    ('Bibliotecário Padrão',     'bibliotecario@biblioteca.com', '$2y$10$7Ngd2Yy00C2hLn7hly.FQ.a9CnO0NnM5WWHmt7i49PIzsctO7Bgkq', 'bibliotecario'),
    ('Leitor Demonstração',      'leitor@biblioteca.com',        '$2y$10$5Jo1TvDJeF.WgXYCbdzKT.tdqeVULajkUYWhDvu.MC7ZWLykege.q', 'leitor')
ON CONFLICT DO NOTHING;
