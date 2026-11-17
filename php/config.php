<?php
define('DB_PATH',     __DIR__ . '/../database/paipede.db');
define('SESSION_NAME','paipede_sess');

function getDB(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;

    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $pdo = new PDO('sqlite:' . DB_PATH);
    $pdo->setAttribute(PDO::ATTR_ERRMODE,            PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->exec('PRAGMA journal_mode=WAL');
    $pdo->exec('PRAGMA foreign_keys=ON');

    initDB($pdo);
    return $pdo;
}

function initDB(PDO $pdo): void {
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS usuario (
            id_usuario   INTEGER PRIMARY KEY AUTOINCREMENT,
            nome         TEXT    NOT NULL,
            email        TEXT    NOT NULL UNIQUE,
            senha_hash   TEXT    NOT NULL,
            tema         TEXT    DEFAULT 'dark',
            notificacoes INTEGER DEFAULT 1,
            criado_em    TEXT    DEFAULT (datetime('now'))
        );

        CREATE TABLE IF NOT EXISTS categoria (
            id_categoria INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario   INTEGER NOT NULL,
            nome         TEXT    NOT NULL,
            tipo         TEXT    NOT NULL DEFAULT 'despesa',
            icone        TEXT    DEFAULT 'fa-tag',
            cor          TEXT    DEFAULT '#e50925',
            bloqueada    INTEGER DEFAULT 0,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS conta (
            id_conta           INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario         INTEGER NOT NULL,
            nome               TEXT    NOT NULL,
            instituicao        TEXT    DEFAULT '',
            tipo_conta         TEXT    DEFAULT 'corrente',
            saldo_inicial      REAL    DEFAULT 0,
            data_saldo_inicial TEXT,
            cor                TEXT    DEFAULT '#e50925',
            criado_em          TEXT    DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS cartao (
            id_cartao      INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario     INTEGER NOT NULL,
            nome           TEXT    NOT NULL,
            ultimos_4      TEXT    DEFAULT '',
            limite_total   REAL    DEFAULT 0,
            dia_fechamento INTEGER DEFAULT 1,
            dia_vencimento INTEGER DEFAULT 10,
            cor            TEXT    DEFAULT '#e50925',
            criado_em      TEXT    DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS fatura (
            id_fatura       INTEGER PRIMARY KEY AUTOINCREMENT,
            id_cartao       INTEGER NOT NULL,
            competencia     TEXT    NOT NULL,
            data_fechamento TEXT,
            data_vencimento TEXT,
            data_pagamento  TEXT,
            valor_total     REAL    DEFAULT 0,
            situacao        TEXT    DEFAULT 'aberta',
            FOREIGN KEY (id_cartao) REFERENCES cartao(id_cartao) ON DELETE CASCADE
        );

        CREATE TABLE IF NOT EXISTS transacao (
            id_transacao    INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario      INTEGER NOT NULL,
            id_conta        INTEGER,
            id_cartao       INTEGER,
            id_categoria    INTEGER,
            descricao       TEXT    NOT NULL,
            valor           REAL    NOT NULL,
            tipo            TEXT    NOT NULL,
            data_transacao  TEXT    NOT NULL,
            data_vencimento TEXT,
            data_efetivacao TEXT,
            situacao        TEXT    DEFAULT 'pago',
            observacao      TEXT    DEFAULT '',
            criado_em       TEXT    DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
            FOREIGN KEY (id_conta)     REFERENCES conta(id_conta)        ON DELETE SET NULL,
            FOREIGN KEY (id_cartao)    REFERENCES cartao(id_cartao)       ON DELETE SET NULL,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS orcamento (
            id_orcamento   INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario     INTEGER NOT NULL,
            id_categoria   INTEGER,
            mes_referencia TEXT    NOT NULL,
            valor_limite   REAL    NOT NULL,
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
        );

        CREATE TABLE IF NOT EXISTS meta (
            id_meta         INTEGER PRIMARY KEY AUTOINCREMENT,
            id_usuario      INTEGER NOT NULL,
            nome            TEXT    NOT NULL,
            descricao       TEXT    DEFAULT '',
            valor_alvo      REAL    NOT NULL,
            valor_acumulado REAL    DEFAULT 0,
            data_limite     TEXT    NOT NULL,
            emoji           TEXT    DEFAULT '🎯',
            criado_em       TEXT    DEFAULT (datetime('now')),
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        );
    ");
}

function startSession(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_name(SESSION_NAME);
        session_start();
    }
}

function getSessionUser(): ?array {
    startSession();
    return $_SESSION['user'] ?? null;
}

function requireLogin(): array {
    $user = getSessionUser();
    if (!$user) { header('Location: login.php'); exit; }
    return $user;
}

function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function seedCategorias(int $uid): void {
    $pdo = getDB();
    $n = $pdo->prepare('SELECT COUNT(*) FROM categoria WHERE id_usuario = ?');
    $n->execute([$uid]);
    if ($n->fetchColumn() > 0) return;

    $cats = [
        ['Salário',     'receita', 'fa-briefcase',      '#22c55e'],
        ['Freelance',   'receita', 'fa-laptop-code',    '#3b82f6'],
        ['Mercado',     'despesa', 'fa-cart-shopping',  '#f59e0b'],
        ['Transporte',  'despesa', 'fa-car',            '#6366f1'],
        ['Saúde',       'despesa', 'fa-heart-pulse',    '#ef4444'],
        ['Lazer',       'despesa', 'fa-gamepad',        '#a855f7'],
        ['Contas',      'despesa', 'fa-file-invoice',   '#f97316'],
        ['Educação',    'despesa', 'fa-graduation-cap', '#14b8a6'],
        ['Alimentação', 'despesa', 'fa-utensils',       '#eab308'],
        ['Outros',      'despesa', 'fa-ellipsis',       '#64748b'],
    ];
    $s = $pdo->prepare('INSERT INTO categoria (id_usuario,nome,tipo,icone,cor,bloqueada) VALUES (?,?,?,?,?,1)');
    foreach ($cats as $c) $s->execute(array_merge([$uid], $c));
}
