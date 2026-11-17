<?php
// ============================================================
//  PAI PEDE — Instalador automático do banco de dados
//  Acesse: http://localhost/paipede/instalar.php
// ============================================================

$host = 'localhost';
$user = 'root';
$pass = '';        // ← coloque sua senha do MySQL aqui se tiver
$port = '3306';

$erros = [];
$ok    = [];

try {
    // Conecta sem selecionar banco ainda
    $pdo = new PDO(
        "mysql:host=$host;port=$port;charset=utf8mb4",
        $user, $pass,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // 1. Cria o banco
    $pdo->exec("CREATE DATABASE IF NOT EXISTS paipede CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $ok[] = "✅ Banco de dados <strong>paipede</strong> criado (ou já existia).";

    $pdo->exec("USE paipede");

    // 2. Tabelas
    $tabelas = [

        "usuario" => "CREATE TABLE IF NOT EXISTS usuario (
            id_usuario    INT AUTO_INCREMENT PRIMARY KEY,
            nome          VARCHAR(120) NOT NULL,
            email         VARCHAR(180) NOT NULL UNIQUE,
            senha_hash    VARCHAR(255) NOT NULL,
            tema          ENUM('dark','light') DEFAULT 'dark',
            notificacoes  TINYINT(1) DEFAULT 1,
            criado_em     DATETIME DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB",

        "categoria" => "CREATE TABLE IF NOT EXISTS categoria (
            id_categoria  INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario    INT NOT NULL,
            nome          VARCHAR(80) NOT NULL,
            tipo          ENUM('receita','despesa') NOT NULL,
            icone         VARCHAR(60) DEFAULT 'fa-tag',
            cor           VARCHAR(20) DEFAULT '#e50925',
            bloqueada     TINYINT(1) DEFAULT 0,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        "conta" => "CREATE TABLE IF NOT EXISTS conta (
            id_conta           INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario         INT NOT NULL,
            nome               VARCHAR(100) NOT NULL,
            instituicao        VARCHAR(100) DEFAULT '',
            tipo_conta         ENUM('corrente','poupanca','investimento','outro') DEFAULT 'corrente',
            saldo_inicial      DECIMAL(15,2) DEFAULT 0.00,
            data_saldo_inicial DATE,
            cor                VARCHAR(20) DEFAULT '#e50925',
            criado_em          DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        "cartao" => "CREATE TABLE IF NOT EXISTS cartao (
            id_cartao       INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario      INT NOT NULL,
            nome            VARCHAR(100) NOT NULL,
            ultimos_4       CHAR(4) DEFAULT '',
            limite_total    DECIMAL(15,2) DEFAULT 0.00,
            dia_fechamento  TINYINT DEFAULT 1,
            dia_vencimento  TINYINT DEFAULT 10,
            cor             VARCHAR(20) DEFAULT '#e50925',
            criado_em       DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        "fatura" => "CREATE TABLE IF NOT EXISTS fatura (
            id_fatura       INT AUTO_INCREMENT PRIMARY KEY,
            id_cartao       INT NOT NULL,
            competencia     VARCHAR(7) NOT NULL,
            data_fechamento DATE,
            data_vencimento DATE,
            data_pagamento  DATE,
            valor_total     DECIMAL(15,2) DEFAULT 0.00,
            situacao        ENUM('aberta','fechada','paga') DEFAULT 'aberta',
            FOREIGN KEY (id_cartao) REFERENCES cartao(id_cartao) ON DELETE CASCADE
        ) ENGINE=InnoDB",

        "transacao" => "CREATE TABLE IF NOT EXISTS transacao (
            id_transacao    INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario      INT NOT NULL,
            id_conta        INT,
            id_cartao       INT,
            id_categoria    INT,
            descricao       VARCHAR(200) NOT NULL,
            valor           DECIMAL(15,2) NOT NULL,
            tipo            ENUM('receita','despesa') NOT NULL,
            data_transacao  DATE NOT NULL,
            data_vencimento DATE,
            data_efetivacao DATE,
            situacao        ENUM('pendente','pago','cancelado') DEFAULT 'pago',
            observacao      TEXT,
            criado_em       DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
            FOREIGN KEY (id_conta)     REFERENCES conta(id_conta)        ON DELETE SET NULL,
            FOREIGN KEY (id_cartao)    REFERENCES cartao(id_cartao)       ON DELETE SET NULL,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
        ) ENGINE=InnoDB",

        "orcamento" => "CREATE TABLE IF NOT EXISTS orcamento (
            id_orcamento    INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario      INT NOT NULL,
            id_categoria    INT,
            mes_referencia  VARCHAR(7) NOT NULL,
            valor_limite    DECIMAL(15,2) NOT NULL,
            FOREIGN KEY (id_usuario)   REFERENCES usuario(id_usuario)    ON DELETE CASCADE,
            FOREIGN KEY (id_categoria) REFERENCES categoria(id_categoria) ON DELETE SET NULL
        ) ENGINE=InnoDB",

        "meta" => "CREATE TABLE IF NOT EXISTS meta (
            id_meta         INT AUTO_INCREMENT PRIMARY KEY,
            id_usuario      INT NOT NULL,
            nome            VARCHAR(120) NOT NULL,
            descricao       TEXT,
            valor_alvo      DECIMAL(15,2) NOT NULL,
            valor_acumulado DECIMAL(15,2) DEFAULT 0.00,
            data_limite     DATE NOT NULL,
            emoji           VARCHAR(10) DEFAULT '🎯',
            criado_em       DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (id_usuario) REFERENCES usuario(id_usuario) ON DELETE CASCADE
        ) ENGINE=InnoDB",
    ];

    foreach ($tabelas as $nome => $sql) {
        $pdo->exec($sql);
        $ok[] = "✅ Tabela <strong>$nome</strong> criada (ou já existia).";
    }

    // 3. Índices (ignora se já existirem)
    $indices = [
        "CREATE INDEX idx_transacao_usuario ON transacao(id_usuario)",
        "CREATE INDEX idx_transacao_data    ON transacao(data_transacao)",
        "CREATE INDEX idx_fatura_cartao     ON fatura(id_cartao)",
    ];
    foreach ($indices as $idx) {
        try { $pdo->exec($idx); } catch (Exception $e) { /* já existe */ }
    }
    $ok[] = "✅ Índices criados.";

    // 4. Verifica conexão final
    $pdo->exec("USE paipede");
    $count = $pdo->query("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='paipede'")->fetchColumn();
    $ok[] = "✅ Banco verificado: <strong>$count tabelas</strong> encontradas.";

} catch (PDOException $e) {
    $erros[] = "❌ Erro de conexão: " . $e->getMessage();
    $erros[] = "👉 Verifique se o MySQL está rodando e se as credenciais em <code>instalar.php</code> estão corretas.";
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Instalação</title>
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{font-family:'Segoe UI',sans-serif;background:#0a0a0a;color:#fff;min-height:100vh;
      display:flex;align-items:center;justify-content:center;padding:24px}
    .box{background:#111;border:1px solid #1e1e1e;border-radius:20px;padding:40px;width:100%;max-width:560px}
    .logo{display:flex;align-items:center;gap:12px;margin-bottom:32px}
    .hex{width:44px;height:44px;background:linear-gradient(135deg,#e50925,#8b0000);
      clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
      display:flex;align-items:center;justify-content:center;font-size:18px;font-weight:900}
    .logo-name{font-size:24px;font-weight:900;letter-spacing:3px}
    h1{font-size:20px;font-weight:800;margin-bottom:8px}
    p.sub{font-size:14px;color:#555;margin-bottom:28px}
    .item{padding:10px 14px;border-radius:10px;font-size:14px;margin-bottom:8px;line-height:1.5}
    .item.ok{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.2);color:#86efac}
    .item.err{background:rgba(229,9,37,.08);border:1px solid rgba(229,9,37,.2);color:#fca5a5}
    .btn{display:inline-flex;align-items:center;gap:8px;margin-top:24px;padding:13px 28px;
      background:linear-gradient(135deg,#e50925,#8b0000);border:none;border-radius:10px;
      color:#fff;font-size:15px;font-weight:700;cursor:pointer;text-decoration:none;transition:opacity .2s}
    .btn:hover{opacity:.9}
    .btn.outline{background:transparent;border:1px solid #333;color:#ccc;margin-left:10px}
    .btn.outline:hover{border-color:#e50925;color:#fff}
    code{background:#1e1e1e;padding:2px 6px;border-radius:4px;font-size:13px;color:#f59e0b}
  </style>
</head>
<body>
<div class="box">
  <div class="logo">
    <div class="hex">P</div>
    <span class="logo-name">PAI PEDE</span>
  </div>
  <h1>Instalação do banco de dados</h1>
  <p class="sub">Criando as tabelas no MySQL...</p>

  <?php foreach ($ok as $msg): ?>
    <div class="item ok"><?= $msg ?></div>
  <?php endforeach; ?>

  <?php foreach ($erros as $msg): ?>
    <div class="item err"><?= $msg ?></div>
  <?php endforeach; ?>

  <?php if (empty($erros)): ?>
    <div class="item ok" style="margin-top:16px;font-weight:700;font-size:15px">
      🎉 Instalação concluída! O sistema está pronto para uso.
    </div>
    <a href="pages/cadastro.php" class="btn">🚀 Ir para o sistema</a>
    <a href="index.html" class="btn outline">← Página inicial</a>
  <?php else: ?>
    <div style="margin-top:20px;font-size:13px;color:#555;line-height:1.7">
      <strong style="color:#f59e0b">Como resolver:</strong><br>
      1. Abra o XAMPP e inicie o <strong>MySQL</strong><br>
      2. Se tiver senha no MySQL, edite a linha <code>$pass = '';</code> neste arquivo<br>
      3. Recarregue esta página
    </div>
    <a href="instalar.php" class="btn" style="margin-top:20px">🔄 Tentar novamente</a>
  <?php endif; ?>
</div>
</body>
</html>
