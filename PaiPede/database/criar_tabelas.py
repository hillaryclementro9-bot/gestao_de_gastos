from database.conexao import get_conn

def criar_tabelas():
    conn = get_conn()
    c = conn.cursor()
    c.executescript("""
        PRAGMA foreign_keys = ON;

        CREATE TABLE IF NOT EXISTS usuario (
            id_usuario         INTEGER PRIMARY KEY AUTOINCREMENT,
            nome               TEXT    NOT NULL,
            email              TEXT    NOT NULL UNIQUE,
            senha_hash         TEXT    NOT NULL,
            tema               TEXT    DEFAULT 'escuro',
            notificacoes_ativas INTEGER DEFAULT 1,
            data_cadastro      DATETIME DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS categoria (
            id_categoria INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario   INTEGER,
            nome         TEXT NOT NULL,
            tipo         TEXT NOT NULL,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS conta (
            id_conta           INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario         INTEGER NOT NULL,
            nome               TEXT    NOT NULL,
            instituicao        TEXT    DEFAULT '',
            tipo_conta         TEXT    DEFAULT 'corrente',
            saldo_inicial      REAL    DEFAULT 0,
            data_saldo_inicial DATE,
            data_criacao       DATETIME DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cartao (
            id_cartao        INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario       INTEGER NOT NULL,
            nome             TEXT    NOT NULL,
            ultimos_4_digitos TEXT   DEFAULT '',
            limite_total     REAL    DEFAULT 0,
            dia_fechamento   INTEGER DEFAULT 1,
            dia_vencimento   INTEGER DEFAULT 10,
            id_conta         INTEGER,
            data_criacao     DATETIME DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE,
            FOREIGN KEY (id_conta)   REFERENCES conta(id_conta)     ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS fatura (
            id_fatura       INTEGER PRIMARY KEY AUTOINCREMENT,
            id_cartao       INTEGER NOT NULL,
            competencia     TEXT    NOT NULL,
            data_fechamento DATE,
            data_vencimento DATE,
            data_pagamento  DATE,
            valor_total     REAL    DEFAULT 0,
            situacao        TEXT    DEFAULT 'Pendente',
            FOREIGN KEY (id_cartao) REFERENCES cartao(id_cartao) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS transacao (
            id_transacao   INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario     INTEGER NOT NULL,
            id_conta       INTEGER,
            id_cartao      INTEGER,
            id_categoria   INTEGER NOT NULL,
            descricao      TEXT    NOT NULL,
            valor          REAL    NOT NULL,
            tipo           TEXT    NOT NULL,
            data_transacao DATE    NOT NULL,
            data_vencimento DATE,
            data_efetivacao DATE,
            situacao       TEXT    DEFAULT 'pago',
            id_fatura      INTEGER,
            data_criacao   DATETIME DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)     ON DELETE CASCADE,
            FOREIGN KEY (id_conta)     REFERENCES conta(id_conta)         ON DELETE SET NULL,
            FOREIGN KEY (id_cartao)    REFERENCES cartao(id_cartao)        ON DELETE SET NULL,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria)  ON DELETE RESTRICT,
            FOREIGN KEY (id_fatura)    REFERENCES fatura(id_fatura)        ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS orcamento (
            id_orcamento   INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario     INTEGER NOT NULL,
            id_categoria   INTEGER,
            mes_referencia TEXT    NOT NULL,
            valor_limite   REAL    NOT NULL,
            data_criacao   DATETIME DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS meta (
            id_meta         INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario      INTEGER NOT NULL,
            nome            TEXT    NOT NULL,
            valor_alvo      REAL    NOT NULL,
            valor_acumulado REAL    DEFAULT 0,
            data_limite     DATE,
            data_criacao    DATETIME DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE INDEX IF NOT EXISTS idx_transacao_usuario ON transacao(id_usuario);
        CREATE INDEX IF NOT EXISTS idx_transacao_data    ON transacao(data_transacao);
        CREATE INDEX IF NOT EXISTS idx_transacao_tipo    ON transacao(tipo);
        CREATE INDEX IF NOT EXISTS idx_fatura_cartao     ON fatura(id_cartao);
        CREATE INDEX IF NOT EXISTS idx_categoria_usuario ON categoria(id_usuario);
    """)
    conn.commit()
    conn.close()
    _seed_categorias_padrao()

def _seed_categorias_padrao():
    from database.conexao import query, execute
    existe = query("SELECT 1 FROM categoria WHERE id_usuario IS NULL LIMIT 1")
    if existe:
        return
    cats = [
        ('Salário',      'receita'),
        ('Freelance',    'receita'),
        ('Outros recebimentos', 'receita'),
        ('Mercado',      'despesa'),
        ('Alimentação',  'despesa'),
        ('Transporte',   'despesa'),
        ('Moradia',      'despesa'),
        ('Saúde',        'despesa'),
        ('Educação',     'despesa'),
        ('Lazer',        'despesa'),
        ('Contas',       'despesa'),
        ('Outros',       'despesa'),
    ]
    for nome, tipo in cats:
        execute("INSERT INTO categoria (id_usuario, nome, tipo) VALUES (NULL, ?, ?)", (nome, tipo))
