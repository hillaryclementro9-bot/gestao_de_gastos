-- ============================================================
--  PAI PEDE — Banco de dados MySQL
--  Importar via phpMyAdmin ou: mysql -u root -p < paipede.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS paipede CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE paipede;

-- 1. USUARIO
CREATE TABLE IF NOT EXISTS usuario (
    id_usuario        INT AUTO_INCREMENT PRIMARY KEY,
    nome              VARCHAR(120) NOT NULL,
    email             VARCHAR(180) NOT NULL UNIQUE,
    senha_hash        VARCHAR(255) NOT NULL,
    tema              ENUM('dark','light') DEFAULT 'dark',
    notificacoes      TINYINT(1) DEFAULT 1,
    criado_em         DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. CATEGORIA
CREATE TABLE IF NOT EXISTS categoria (
    id_categoria  INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario    INT NOT NULL,
    nome          VARCHAR(80) NOT NULL,
    tipo          ENUM('receita','despesa') NOT NULL,
    icone         VARCHAR(60) DEFAULT 'fa-tag',
    cor           VARCHAR(20) DEFAULT '#e50925',
    bloqueada     TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. CONTA
CREATE TABLE IF NOT EXISTS conta (
    id_conta          INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    nome              VARCHAR(100) NOT NULL,
    instituicao       VARCHAR(100) DEFAULT '',
    tipo_conta        ENUM('corrente','poupanca','investimento','outro') DEFAULT 'corrente',
    saldo_inicial     DECIMAL(15,2) DEFAULT 0.00,
    data_saldo_inicial DATE,
    cor               VARCHAR(20) DEFAULT '#e50925',
    criado_em         DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 4. CARTAO
CREATE TABLE IF NOT EXISTS cartao (
    id_cartao         INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    nome              VARCHAR(100) NOT NULL,
    ultimos_4         CHAR(4) DEFAULT '',
    limite_total      DECIMAL(15,2) DEFAULT 0.00,
    dia_fechamento    TINYINT DEFAULT 1,
    dia_vencimento    TINYINT DEFAULT 10,
    cor               VARCHAR(20) DEFAULT '#e50925',
    criado_em         DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 5. FATURA
CREATE TABLE IF NOT EXISTS fatura (
    id_fatura         INT AUTO_INCREMENT PRIMARY KEY,
    id_cartao         INT NOT NULL,
    competencia       VARCHAR(7) NOT NULL COMMENT 'YYYY-MM',
    data_fechamento   DATE,
    data_vencimento   DATE,
    data_pagamento    DATE,
    valor_total       DECIMAL(15,2) DEFAULT 0.00,
    situacao          ENUM('aberta','fechada','paga') DEFAULT 'aberta',
    FOREIGN KEY (id_cartao) REFERENCES cartao(id_cartao) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. TRANSACAO
CREATE TABLE IF NOT EXISTS transacao (
    id_transacao      INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    id_conta          INT,
    id_cartao         INT,
    id_categoria      INT,
    descricao         VARCHAR(200) NOT NULL,
    valor             DECIMAL(15,2) NOT NULL,
    tipo              ENUM('receita','despesa') NOT NULL,
    data_transacao    DATE NOT NULL,
    data_vencimento   DATE,
    data_efetivacao   DATE,
    situacao          ENUM('pendente','pago','cancelado') DEFAULT 'pago',
    observacao        TEXT,
    criado_em         DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)     ON DELETE CASCADE,
    FOREIGN KEY (id_conta)     REFERENCES conta(id_conta)         ON DELETE SET NULL,
    FOREIGN KEY (id_cartao)    REFERENCES cartao(id_cartao)        ON DELETE SET NULL,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)  ON DELETE SET NULL
) ENGINE=InnoDB;

-- 7. ORCAMENTO
CREATE TABLE IF NOT EXISTS orcamento (
    id_orcamento      INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    id_categoria      INT,
    mes_referencia    VARCHAR(7) NOT NULL COMMENT 'YYYY-MM',
    valor_limite      DECIMAL(15,2) NOT NULL,
    FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
    FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 8. META
CREATE TABLE IF NOT EXISTS meta (
    id_meta           INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario        INT NOT NULL,
    nome              VARCHAR(120) NOT NULL,
    descricao         TEXT,
    valor_alvo        DECIMAL(15,2) NOT NULL,
    valor_acumulado   DECIMAL(15,2) DEFAULT 0.00,
    data_limite       DATE NOT NULL,
    emoji             VARCHAR(10) DEFAULT '🎯',
    criado_em         DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ── ÍNDICES ──────────────────────────────────────────────────
CREATE INDEX idx_transacao_usuario ON transacao(id_usuario);
CREATE INDEX idx_transacao_data    ON transacao(data_transacao);
CREATE INDEX idx_fatura_cartao     ON fatura(id_cartao);
