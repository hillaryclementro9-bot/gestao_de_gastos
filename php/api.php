<?php
require_once __DIR__ . '/config.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(200); exit; }

$action = $_GET['action'] ?? '';
$body   = json_decode(file_get_contents('php://input'), true) ?? [];

try { $pdo = getDB(); }
catch (Exception $e) { jsonResponse(['ok'=>false,'msg'=>'Erro no banco: '.$e->getMessage()], 500); }

// ── CADASTRO ─────────────────────────────────────────────────
if ($action === 'cadastro') {
    $nome  = trim($body['nome']      ?? '');
    $email = trim($body['email']     ?? '');
    $senha = $body['senha']          ?? '';
    $conf  = $body['confirmar']      ?? '';

    if (!$nome || !$email || !$senha)
        jsonResponse(['ok'=>false,'msg'=>'Preencha todos os campos.']);
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        jsonResponse(['ok'=>false,'msg'=>'E-mail inválido.']);
    if (strlen($senha) < 6)
        jsonResponse(['ok'=>false,'msg'=>'Senha mínimo 6 caracteres.']);
    if ($senha !== $conf)
        jsonResponse(['ok'=>false,'msg'=>'Senhas não conferem.']);

    $chk = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = ?');
    $chk->execute([$email]);
    if ($chk->fetch()) jsonResponse(['ok'=>false,'msg'=>'E-mail já cadastrado.']);

    $hash = password_hash($senha, PASSWORD_DEFAULT);
    $pdo->prepare('INSERT INTO usuario (nome,email,senha_hash) VALUES (?,?,?)')->execute([$nome,$email,$hash]);
    $id = (int)$pdo->lastInsertId();

    seedCategorias($id);
    startSession();
    $_SESSION['user'] = ['id'=>$id,'nome'=>$nome,'email'=>$email,'tema'=>'dark'];
    jsonResponse(['ok'=>true,'user'=>$_SESSION['user']]);
}

// ── LOGIN ────────────────────────────────────────────────────
if ($action === 'login') {
    $email = trim($body['email'] ?? '');
    $senha = $body['senha'] ?? '';
    if (!$email || !$senha) jsonResponse(['ok'=>false,'msg'=>'Preencha e-mail e senha.']);

    $s = $pdo->prepare('SELECT * FROM usuario WHERE email = ?');
    $s->execute([$email]);
    $u = $s->fetch();
    if (!$u || !password_verify($senha, $u['senha_hash']))
        jsonResponse(['ok'=>false,'msg'=>'E-mail ou senha incorretos.']);

    startSession();
    $_SESSION['user'] = ['id'=>(int)$u['id_usuario'],'nome'=>$u['nome'],'email'=>$u['email'],'tema'=>$u['tema']];
    jsonResponse(['ok'=>true,'user'=>$_SESSION['user']]);
}

// ── LOGOUT ───────────────────────────────────────────────────
if ($action === 'logout') {
    startSession(); session_destroy();
    jsonResponse(['ok'=>true]);
}

// ── SESSION ──────────────────────────────────────────────────
if ($action === 'session') {
    $u = getSessionUser();
    jsonResponse(['ok'=>(bool)$u,'user'=>$u]);
}

// ── RECUPERAR SENHA ──────────────────────────────────────────
if ($action === 'recuperar_senha') {
    $email = trim($body['email'] ?? '');
    $nova  = $body['nova_senha'] ?? '';
    if (!$email || !$nova) jsonResponse(['ok'=>false,'msg'=>'Dados incompletos.']);
    $chk = $pdo->prepare('SELECT id_usuario FROM usuario WHERE email = ?');
    $chk->execute([$email]);
    if (!$chk->fetch()) jsonResponse(['ok'=>false,'msg'=>'E-mail não encontrado.']);
    $pdo->prepare('UPDATE usuario SET senha_hash=? WHERE email=?')
        ->execute([password_hash($nova, PASSWORD_DEFAULT), $email]);
    jsonResponse(['ok'=>true]);
}

// ── AUTH GUARD ───────────────────────────────────────────────
$user   = getSessionUser();
$userId = $user ? (int)$user['id'] : null;
if (!$userId) jsonResponse(['ok'=>false,'msg'=>'Não autenticado.'], 401);

// ── DASHBOARD ────────────────────────────────────────────────
if ($action === 'dashboard') {
    $mes = $body['mes'] ?? date('Y-m');

    // receitas e despesas do mês — SQLite usa strftime
    $s = $pdo->prepare("
        SELECT tipo, COALESCE(SUM(valor),0) as total
        FROM transacao
        WHERE id_usuario=? AND strftime('%Y-%m', data_transacao)=? AND situacao != 'cancelado'
        GROUP BY tipo
    ");
    $s->execute([$userId, $mes]);
    $totais = ['receita'=>0,'despesa'=>0];
    foreach ($s->fetchAll() as $r) $totais[$r['tipo']] = (float)$r['total'];

    // saldo base das contas
    $sc = $pdo->prepare('SELECT COALESCE(SUM(saldo_inicial),0) FROM conta WHERE id_usuario=?');
    $sc->execute([$userId]);
    $saldoBase = (float)$sc->fetchColumn();

    // ajuste pelas transações pagas (todas, não só do mês)
    $st = $pdo->prepare("
        SELECT COALESCE(SUM(CASE WHEN tipo='receita' THEN valor ELSE -valor END),0)
        FROM transacao WHERE id_usuario=? AND situacao='pago'
    ");
    $st->execute([$userId]);
    $saldo = $saldoBase + (float)$st->fetchColumn();

    // despesas por categoria no mês
    $sc2 = $pdo->prepare("
        SELECT c.nome, c.cor, SUM(t.valor) as total
        FROM transacao t
        LEFT JOIN categoria c ON c.id_categoria = t.id_categoria
        WHERE t.id_usuario=? AND t.tipo='despesa'
          AND strftime('%Y-%m', t.data_transacao)=? AND t.situacao!='cancelado'
        GROUP BY t.id_categoria
        ORDER BY total DESC LIMIT 8
    ");
    $sc2->execute([$userId, $mes]);
    $porCategoria = $sc2->fetchAll();

    // próximas contas pendentes — SQLite usa date('now')
    $sp = $pdo->prepare("
        SELECT descricao, valor, data_vencimento, situacao
        FROM transacao
        WHERE id_usuario=? AND situacao='pendente' AND data_vencimento >= date('now')
        ORDER BY data_vencimento ASC LIMIT 5
    ");
    $sp->execute([$userId]);
    $proximas = $sp->fetchAll();

    // metas
    $sm = $pdo->prepare('SELECT * FROM meta WHERE id_usuario=? ORDER BY data_limite ASC LIMIT 4');
    $sm->execute([$userId]);
    $metas = $sm->fetchAll();

    jsonResponse(['ok'=>true,'saldo'=>$saldo,'receitas'=>$totais['receita'],
        'despesas'=>$totais['despesa'],'por_categoria'=>$porCategoria,
        'proximas'=>$proximas,'metas'=>$metas]);
}

// ── TRANSAÇÕES ───────────────────────────────────────────────
if ($action === 'get_transacoes') {
    $params = [$userId];
    $sql = "SELECT t.*, c.nome as categoria_nome, c.cor as categoria_cor,
                   ct.nome as conta_nome, ca.nome as cartao_nome
            FROM transacao t
            LEFT JOIN categoria c  ON c.id_categoria = t.id_categoria
            LEFT JOIN conta ct     ON ct.id_conta    = t.id_conta
            LEFT JOIN cartao ca    ON ca.id_cartao   = t.id_cartao
            WHERE t.id_usuario = ?";

    if (!empty($body['mes']))     { $sql .= " AND strftime('%Y-%m', t.data_transacao)=?"; $params[] = $body['mes']; }
    if (!empty($body['tipo']))    { $sql .= " AND t.tipo=?";     $params[] = $body['tipo']; }
    if (!empty($body['situacao'])){ $sql .= " AND t.situacao=?"; $params[] = $body['situacao']; }
    if (!empty($body['busca']))   { $sql .= " AND t.descricao LIKE ?"; $params[] = '%'.$body['busca'].'%'; }

    $sql .= " ORDER BY t.data_transacao DESC, t.id_transacao DESC";
    $s = $pdo->prepare($sql);
    $s->execute($params);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_transacao') {
    $d = $body;
    if (empty($d['descricao']) || empty($d['valor']) || empty($d['tipo']) || empty($d['data_transacao']))
        jsonResponse(['ok'=>false,'msg'=>'Campos obrigatórios faltando.']);

    $ic = $d['id_conta']     ?: null;
    $ica= $d['id_cartao']    ?: null;
    $icat=$d['id_categoria'] ?: null;

    if (!empty($d['id_transacao'])) {
        $pdo->prepare("UPDATE transacao SET descricao=?,valor=?,tipo=?,data_transacao=?,
            data_vencimento=?,data_efetivacao=?,situacao=?,observacao=?,id_conta=?,id_cartao=?,id_categoria=?
            WHERE id_transacao=? AND id_usuario=?")
            ->execute([$d['descricao'],$d['valor'],$d['tipo'],$d['data_transacao'],
                $d['data_vencimento']??null,$d['data_efetivacao']??null,$d['situacao']??'pago',
                $d['observacao']??'',$ic,$ica,$icat,$d['id_transacao'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_transacao']]);
    } else {
        $pdo->prepare("INSERT INTO transacao
            (id_usuario,descricao,valor,tipo,data_transacao,data_vencimento,data_efetivacao,situacao,observacao,id_conta,id_cartao,id_categoria)
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?)")
            ->execute([$userId,$d['descricao'],$d['valor'],$d['tipo'],$d['data_transacao'],
                $d['data_vencimento']??null,$d['data_efetivacao']??null,$d['situacao']??'pago',
                $d['observacao']??'',$ic,$ica,$icat]);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'delete_transacao') {
    $pdo->prepare('DELETE FROM transacao WHERE id_transacao=? AND id_usuario=?')
        ->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── CONTAS ───────────────────────────────────────────────────
if ($action === 'get_contas') {
    $s = $pdo->prepare('SELECT * FROM conta WHERE id_usuario=? ORDER BY id_conta ASC');
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_conta') {
    $d = $body;
    if (!empty($d['id_conta'])) {
        $pdo->prepare("UPDATE conta SET nome=?,instituicao=?,tipo_conta=?,saldo_inicial=?,data_saldo_inicial=?,cor=?
            WHERE id_conta=? AND id_usuario=?")
            ->execute([$d['nome'],$d['instituicao']??'',$d['tipo_conta']??'corrente',
                $d['saldo_inicial']??0,$d['data_saldo_inicial']??null,$d['cor']??'#e50925',
                $d['id_conta'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_conta']]);
    } else {
        $pdo->prepare("INSERT INTO conta (id_usuario,nome,instituicao,tipo_conta,saldo_inicial,data_saldo_inicial,cor)
            VALUES (?,?,?,?,?,?,?)")
            ->execute([$userId,$d['nome'],$d['instituicao']??'',$d['tipo_conta']??'corrente',
                $d['saldo_inicial']??0,$d['data_saldo_inicial']??null,$d['cor']??'#e50925']);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'delete_conta') {
    $pdo->prepare('DELETE FROM conta WHERE id_conta=? AND id_usuario=?')->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── CARTÕES ──────────────────────────────────────────────────
if ($action === 'get_cartoes') {
    $s = $pdo->prepare('SELECT * FROM cartao WHERE id_usuario=? ORDER BY id_cartao ASC');
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_cartao') {
    $d = $body;
    if (!empty($d['id_cartao'])) {
        $pdo->prepare("UPDATE cartao SET nome=?,ultimos_4=?,limite_total=?,dia_fechamento=?,dia_vencimento=?,cor=?
            WHERE id_cartao=? AND id_usuario=?")
            ->execute([$d['nome'],$d['ultimos_4']??'',$d['limite_total']??0,
                $d['dia_fechamento']??1,$d['dia_vencimento']??10,$d['cor']??'#e50925',
                $d['id_cartao'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_cartao']]);
    } else {
        $pdo->prepare("INSERT INTO cartao (id_usuario,nome,ultimos_4,limite_total,dia_fechamento,dia_vencimento,cor)
            VALUES (?,?,?,?,?,?,?)")
            ->execute([$userId,$d['nome'],$d['ultimos_4']??'',$d['limite_total']??0,
                $d['dia_fechamento']??1,$d['dia_vencimento']??10,$d['cor']??'#e50925']);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'delete_cartao') {
    $pdo->prepare('DELETE FROM cartao WHERE id_cartao=? AND id_usuario=?')->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── FATURAS ──────────────────────────────────────────────────
if ($action === 'get_faturas') {
    $s = $pdo->prepare("
        SELECT f.*, ca.nome as cartao_nome
        FROM fatura f
        JOIN cartao ca ON ca.id_cartao = f.id_cartao
        WHERE ca.id_usuario=?
        ORDER BY f.competencia DESC
    ");
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_fatura') {
    $d = $body;
    $chk = $pdo->prepare('SELECT id_cartao FROM cartao WHERE id_cartao=? AND id_usuario=?');
    $chk->execute([$d['id_cartao'],$userId]);
    if (!$chk->fetch()) jsonResponse(['ok'=>false,'msg'=>'Cartão inválido.']);

    if (!empty($d['id_fatura'])) {
        $pdo->prepare("UPDATE fatura SET competencia=?,data_fechamento=?,data_vencimento=?,data_pagamento=?,situacao=?
            WHERE id_fatura=?")
            ->execute([$d['competencia'],$d['data_fechamento']??null,$d['data_vencimento']??null,
                $d['data_pagamento']??null,$d['situacao']??'aberta',$d['id_fatura']]);
        jsonResponse(['ok'=>true,'id'=>$d['id_fatura']]);
    } else {
        $pdo->prepare("INSERT INTO fatura (id_cartao,competencia,data_fechamento,data_vencimento,data_pagamento,situacao)
            VALUES (?,?,?,?,?,?)")
            ->execute([$d['id_cartao'],$d['competencia'],$d['data_fechamento']??null,
                $d['data_vencimento']??null,$d['data_pagamento']??null,$d['situacao']??'aberta']);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'pagar_fatura') {
    // SQLite não suporta UPDATE com JOIN — usa subquery
    $pdo->prepare("
        UPDATE fatura SET situacao='paga', data_pagamento=?
        WHERE id_fatura=?
          AND id_cartao IN (SELECT id_cartao FROM cartao WHERE id_usuario=?)
    ")->execute([$body['data_pagamento']??date('Y-m-d'), $body['id'], $userId]);
    jsonResponse(['ok'=>true]);
}

if ($action === 'delete_fatura') {
    // SQLite não suporta DELETE com JOIN — usa subquery
    $pdo->prepare("
        DELETE FROM fatura
        WHERE id_fatura=?
          AND id_cartao IN (SELECT id_cartao FROM cartao WHERE id_usuario=?)
    ")->execute([$body['id'], $userId]);
    jsonResponse(['ok'=>true]);
}

// ── METAS ────────────────────────────────────────────────────
if ($action === 'get_metas') {
    $s = $pdo->prepare('SELECT * FROM meta WHERE id_usuario=? ORDER BY data_limite ASC');
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_meta') {
    $d = $body;
    if (!empty($d['id_meta'])) {
        $pdo->prepare("UPDATE meta SET nome=?,descricao=?,valor_alvo=?,valor_acumulado=?,data_limite=?,emoji=?
            WHERE id_meta=? AND id_usuario=?")
            ->execute([$d['nome'],$d['descricao']??'',$d['valor_alvo'],$d['valor_acumulado']??0,
                $d['data_limite'],$d['emoji']??'🎯',$d['id_meta'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_meta']]);
    } else {
        $pdo->prepare("INSERT INTO meta (id_usuario,nome,descricao,valor_alvo,valor_acumulado,data_limite,emoji)
            VALUES (?,?,?,?,?,?,?)")
            ->execute([$userId,$d['nome'],$d['descricao']??'',$d['valor_alvo'],
                $d['valor_acumulado']??0,$d['data_limite'],$d['emoji']??'🎯']);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'depositar_meta') {
    $pdo->prepare("UPDATE meta SET valor_acumulado = valor_acumulado + ? WHERE id_meta=? AND id_usuario=?")
        ->execute([$body['valor'],$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

if ($action === 'delete_meta') {
    $pdo->prepare('DELETE FROM meta WHERE id_meta=? AND id_usuario=?')->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── ORÇAMENTOS ───────────────────────────────────────────────
if ($action === 'get_orcamentos') {
    $mes = $body['mes'] ?? date('Y-m');
    $s = $pdo->prepare("
        SELECT o.*, c.nome as categoria_nome, c.cor as categoria_cor,
               COALESCE((
                   SELECT SUM(t.valor) FROM transacao t
                   WHERE t.id_usuario = o.id_usuario
                     AND t.id_categoria = o.id_categoria
                     AND strftime('%Y-%m', t.data_transacao) = o.mes_referencia
                     AND t.tipo = 'despesa'
                     AND t.situacao != 'cancelado'
               ), 0) as gasto
        FROM orcamento o
        LEFT JOIN categoria c ON c.id_categoria = o.id_categoria
        WHERE o.id_usuario=? AND o.mes_referencia=?
    ");
    $s->execute([$userId, $mes]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_orcamento') {
    $d = $body;
    if (!empty($d['id_orcamento'])) {
        $pdo->prepare("UPDATE orcamento SET id_categoria=?,mes_referencia=?,valor_limite=?
            WHERE id_orcamento=? AND id_usuario=?")
            ->execute([$d['id_categoria']??null,$d['mes_referencia'],$d['valor_limite'],
                $d['id_orcamento'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_orcamento']]);
    } else {
        $pdo->prepare("INSERT INTO orcamento (id_usuario,id_categoria,mes_referencia,valor_limite) VALUES (?,?,?,?)")
            ->execute([$userId,$d['id_categoria']??null,$d['mes_referencia'],$d['valor_limite']]);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'delete_orcamento') {
    $pdo->prepare('DELETE FROM orcamento WHERE id_orcamento=? AND id_usuario=?')->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── CATEGORIAS ───────────────────────────────────────────────
if ($action === 'get_categorias') {
    $s = $pdo->prepare('SELECT * FROM categoria WHERE id_usuario=? ORDER BY tipo, nome');
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetchAll()]);
}

if ($action === 'save_categoria') {
    $d = $body;
    if (!empty($d['id_categoria'])) {
        $pdo->prepare("UPDATE categoria SET nome=?,tipo=?,icone=?,cor=?
            WHERE id_categoria=? AND id_usuario=? AND bloqueada=0")
            ->execute([$d['nome'],$d['tipo']??'despesa',$d['icone']??'fa-tag',
                $d['cor']??'#e50925',$d['id_categoria'],$userId]);
        jsonResponse(['ok'=>true,'id'=>$d['id_categoria']]);
    } else {
        $pdo->prepare("INSERT INTO categoria (id_usuario,nome,tipo,icone,cor) VALUES (?,?,?,?,?)")
            ->execute([$userId,$d['nome'],$d['tipo']??'despesa',$d['icone']??'fa-tag',$d['cor']??'#e50925']);
        jsonResponse(['ok'=>true,'id'=>(int)$pdo->lastInsertId()]);
    }
}

if ($action === 'delete_categoria') {
    $pdo->prepare('DELETE FROM categoria WHERE id_categoria=? AND id_usuario=? AND bloqueada=0')
        ->execute([$body['id'],$userId]);
    jsonResponse(['ok'=>true]);
}

// ── PERFIL ───────────────────────────────────────────────────
if ($action === 'get_perfil') {
    $s = $pdo->prepare('SELECT id_usuario,nome,email,tema,notificacoes FROM usuario WHERE id_usuario=?');
    $s->execute([$userId]);
    jsonResponse(['ok'=>true,'data'=>$s->fetch()]);
}

if ($action === 'save_perfil') {
    $d = $body;
    $pdo->prepare("UPDATE usuario SET nome=?,email=?,tema=?,notificacoes=? WHERE id_usuario=?")
        ->execute([$d['nome'],$d['email'],$d['tema']??'dark',(int)($d['notificacoes']??1),$userId]);
    startSession();
    $_SESSION['user']['nome']  = $d['nome'];
    $_SESSION['user']['email'] = $d['email'];
    $_SESSION['user']['tema']  = $d['tema'] ?? 'dark';
    jsonResponse(['ok'=>true]);
}

if ($action === 'alterar_senha') {
    $atual = $body['senha_atual'] ?? '';
    $nova  = $body['nova_senha']  ?? '';
    if (strlen($nova) < 6) jsonResponse(['ok'=>false,'msg'=>'Nova senha mínimo 6 caracteres.']);
    $s = $pdo->prepare('SELECT senha_hash FROM usuario WHERE id_usuario=?');
    $s->execute([$userId]);
    $u = $s->fetch();
    if (!password_verify($atual, $u['senha_hash'])) jsonResponse(['ok'=>false,'msg'=>'Senha atual incorreta.']);
    $pdo->prepare('UPDATE usuario SET senha_hash=? WHERE id_usuario=?')
        ->execute([password_hash($nova, PASSWORD_DEFAULT), $userId]);
    jsonResponse(['ok'=>true]);
}

jsonResponse(['ok'=>false,'msg'=>'Ação inválida.'], 400);
