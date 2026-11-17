<?php
require_once __DIR__ . '/../php/config.php';
startSession();
if (getSessionUser()) { header('Location: dashboard.php'); exit; }
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Acesso</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--red:#e50925;--red2:#8b0000;--bg:#0a0a0a;--card:#111;--border:#1e1e1e;--muted:#555}
    body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:#fff;min-height:100vh;display:flex}

    .left{flex:1;display:flex;flex-direction:column;justify-content:center;align-items:center;
      background:linear-gradient(145deg,#1a0000 0%,#0d0d0d 60%);padding:60px 40px;
      position:relative;overflow:hidden}
    .left::before{content:'';position:absolute;width:500px;height:500px;
      background:radial-gradient(circle,rgba(229,9,37,.2) 0%,transparent 70%);
      top:-100px;left:-100px;pointer-events:none}
    .left-logo{display:flex;align-items:center;gap:14px;margin-bottom:40px}
    .logo-hex{width:52px;height:52px;background:linear-gradient(135deg,var(--red),var(--red2));
      clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
      display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:900}
    .logo-text{font-size:28px;font-weight:900;letter-spacing:4px}
    .left h1{font-size:32px;font-weight:900;line-height:1.2;margin-bottom:14px;text-align:center}
    .left h1 span{color:var(--red)}
    .left p{font-size:14px;color:#555;text-align:center;max-width:300px;line-height:1.7}
    .feats{margin-top:40px;display:flex;flex-direction:column;gap:16px;width:100%;max-width:300px}
    .feat-row{display:flex;align-items:center;gap:12px}
    .feat-ic{width:34px;height:34px;background:rgba(229,9,37,.1);border:1px solid rgba(229,9,37,.2);
      border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--red);font-size:13px;flex-shrink:0}
    .feat-row span{font-size:13px;color:#555}

    .right{width:500px;display:flex;flex-direction:column;justify-content:center;
      padding:60px 48px;background:var(--card);border-left:1px solid var(--border)}

    /* ABAS */
    .tabs{display:flex;gap:0;margin-bottom:32px;border-bottom:1px solid var(--border)}
    .tab{flex:1;padding:12px;text-align:center;font-size:14px;font-weight:600;color:#444;
      cursor:pointer;border-bottom:2px solid transparent;transition:all .2s}
    .tab.active{color:var(--red);border-bottom-color:var(--red)}

    .form-panel{display:none}
    .form-panel.active{display:block}

    .msg{padding:12px 16px;border-radius:10px;font-size:13px;margin-bottom:18px;
      display:none;align-items:center;gap:10px}
    .msg.err{background:rgba(255,71,87,.08);border:1px solid rgba(255,71,87,.25);color:#ff4757;display:flex}
    .msg.ok{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);color:#22c55e;display:flex}

    .group{margin-bottom:18px}
    .group label{display:block;font-size:11px;font-weight:600;color:#444;text-transform:uppercase;
      letter-spacing:.6px;margin-bottom:7px}
    .wrap{position:relative}
    .wrap i.ic{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#333;font-size:13px;pointer-events:none}
    .wrap input{width:100%;height:48px;background:#1a1a1a;border:1px solid #252525;border-radius:10px;
      padding:0 44px;color:#fff;font-size:14px;outline:none;transition:border-color .2s,background .2s}
    .wrap input::placeholder{color:#333}
    .wrap input:focus{border-color:var(--red);background:#1e1e1e}
    .eye{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;
      color:#333;cursor:pointer;font-size:13px;transition:color .15s}
    .eye:hover{color:var(--red)}

    .btn{width:100%;height:50px;background:linear-gradient(135deg,var(--red),var(--red2));
      border:none;border-radius:10px;color:#fff;font-size:15px;font-weight:700;cursor:pointer;
      display:flex;align-items:center;justify-content:center;gap:10px;
      transition:opacity .2s,transform .1s;margin-top:4px}
    .btn:hover:not(:disabled){opacity:.9;transform:translateY(-1px)}
    .btn:disabled{opacity:.4;cursor:not-allowed}

    .link-btn{background:none;border:none;color:var(--red);font-size:13px;cursor:pointer;
      text-decoration:underline;padding:0}
    .divider{display:flex;align-items:center;gap:12px;margin:20px 0}
    .divider::before,.divider::after{content:'';flex:1;height:1px;background:var(--border)}
    .divider span{font-size:12px;color:#333}
    .footer-link{text-align:center;font-size:14px;color:#444}
    .footer-link a,.footer-link button{color:var(--red);text-decoration:none;font-weight:600;
      background:none;border:none;cursor:pointer;font-size:14px}

    .spinner{width:16px;height:16px;border:2px solid rgba(255,255,255,.3);border-top-color:#fff;
      border-radius:50%;animation:spin .7s linear infinite;display:none}
    @keyframes spin{to{transform:rotate(360deg)}}

    @media(max-width:768px){
      .left{display:none}
      .right{width:100%;padding:40px 24px;border-left:none}
    }
  </style>
</head>
<body>

<div class="left">
  <div class="left-logo">
    <div class="logo-hex">P</div>
    <span class="logo-text">PAI PEDE</span>
  </div>
  <h1>Controle seu <span>dinheiro</span><br>de verdade</h1>
  <p>Gerencie gastos, faturas e metas em um só lugar.</p>
  <div class="feats">
    <div class="feat-row"><div class="feat-ic"><i class="fa fa-chart-pie"></i></div><span>Dashboard completo em tempo real</span></div>
    <div class="feat-row"><div class="feat-ic"><i class="fa fa-apple-whole"></i></div><span>Maçã financeira — saúde do seu bolso</span></div>
    <div class="feat-row"><div class="feat-ic"><i class="fa fa-bullseye"></i></div><span>Metas e orçamentos personalizados</span></div>
    <div class="feat-row"><div class="feat-ic"><i class="fa fa-credit-card"></i></div><span>Controle de cartões e faturas</span></div>
  </div>
</div>

<div class="right">
  <div class="tabs">
    <div class="tab active" onclick="switchTab('login')">Entrar</div>
    <div class="tab" onclick="switchTab('cadastro')">Criar conta</div>
  </div>

  <!-- LOGIN -->
  <div class="form-panel active" id="panel-login">
    <div class="msg" id="msg-login"></div>
    <form onsubmit="fazerLogin(event)">
      <div class="group">
        <label>E-mail</label>
        <div class="wrap"><i class="fa fa-envelope ic"></i>
          <input type="email" id="l-email" placeholder="seu@email.com" required/>
        </div>
      </div>
      <div class="group">
        <label>Senha</label>
        <div class="wrap"><i class="fa fa-lock ic"></i>
          <input type="password" id="l-senha" placeholder="Sua senha" required/>
          <button type="button" class="eye" onclick="togglePw('l-senha','eye-l')"><i class="fa fa-eye" id="eye-l"></i></button>
        </div>
      </div>
      <div style="text-align:right;margin-bottom:16px">
        <button type="button" class="link-btn" onclick="switchTab('recuperar')">Esqueci minha senha</button>
      </div>
      <button type="submit" class="btn" id="btn-login">
        <div class="spinner" id="sp-login"></div>
        <span id="txt-login"><i class="fa fa-right-to-bracket"></i> Entrar</span>
      </button>
    </form>
    <div class="divider"><span>ou</span></div>
    <div class="footer-link">Não tem conta? <button onclick="switchTab('cadastro')">Criar conta grátis</button></div>
  </div>

  <!-- CADASTRO -->
  <div class="form-panel" id="panel-cadastro">
    <div class="msg" id="msg-cad"></div>
    <form onsubmit="fazerCadastro(event)">
      <div class="group">
        <label>Nome completo</label>
        <div class="wrap"><i class="fa fa-user ic"></i>
          <input type="text" id="c-nome" placeholder="Seu nome completo" required/>
        </div>
      </div>
      <div class="group">
        <label>E-mail</label>
        <div class="wrap"><i class="fa fa-envelope ic"></i>
          <input type="email" id="c-email" placeholder="seu@email.com" required/>
        </div>
      </div>
      <div class="group">
        <label>Senha</label>
        <div class="wrap"><i class="fa fa-lock ic"></i>
          <input type="password" id="c-senha" placeholder="Mínimo 6 caracteres" required/>
          <button type="button" class="eye" onclick="togglePw('c-senha','eye-c')"><i class="fa fa-eye" id="eye-c"></i></button>
        </div>
      </div>
      <div class="group">
        <label>Confirmar senha</label>
        <div class="wrap"><i class="fa fa-lock ic"></i>
          <input type="password" id="c-conf" placeholder="Repita a senha" required/>
          <button type="button" class="eye" onclick="togglePw('c-conf','eye-cc')"><i class="fa fa-eye" id="eye-cc"></i></button>
        </div>
      </div>
      <button type="submit" class="btn" id="btn-cad">
        <div class="spinner" id="sp-cad"></div>
        <span id="txt-cad"><i class="fa fa-user-plus"></i> Criar minha conta</span>
      </button>
    </form>
    <div class="divider"><span>ou</span></div>
    <div class="footer-link">Já tem conta? <button onclick="switchTab('login')">Entrar agora</button></div>
  </div>

  <!-- RECUPERAR SENHA -->
  <div class="form-panel" id="panel-recuperar">
    <div class="msg" id="msg-rec"></div>
    <p style="font-size:14px;color:#555;margin-bottom:24px">Informe seu e-mail e defina uma nova senha.</p>
    <form onsubmit="recuperarSenha(event)">
      <div class="group">
        <label>E-mail cadastrado</label>
        <div class="wrap"><i class="fa fa-envelope ic"></i>
          <input type="email" id="r-email" placeholder="seu@email.com" required/>
        </div>
      </div>
      <div class="group">
        <label>Nova senha</label>
        <div class="wrap"><i class="fa fa-lock ic"></i>
          <input type="password" id="r-senha" placeholder="Mínimo 6 caracteres" required/>
        </div>
      </div>
      <button type="submit" class="btn" id="btn-rec">
        <div class="spinner" id="sp-rec"></div>
        <span id="txt-rec"><i class="fa fa-key"></i> Redefinir senha</span>
      </button>
    </form>
    <div class="divider"><span>ou</span></div>
    <div class="footer-link"><button onclick="switchTab('login')">Voltar ao login</button></div>
  </div>
</div>

<script>
const API = '../php/api.php';

function switchTab(tab) {
  document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
  document.querySelectorAll('.form-panel').forEach(p => p.classList.remove('active'));
  const map = {login:0, cadastro:1, recuperar:null};
  if (tab === 'login')    { document.querySelectorAll('.tab')[0].classList.add('active'); }
  if (tab === 'cadastro') { document.querySelectorAll('.tab')[1].classList.add('active'); }
  document.getElementById('panel-' + tab).classList.add('active');
}

function togglePw(id, iconId) {
  const inp = document.getElementById(id);
  const ic  = document.getElementById(iconId);
  inp.type = inp.type === 'password' ? 'text' : 'password';
  ic.className = inp.type === 'text' ? 'fa fa-eye-slash' : 'fa fa-eye';
}

function showMsg(id, msg, type='err') {
  const el = document.getElementById(id);
  el.innerHTML = `<i class="fa fa-${type==='ok'?'circle-check':'circle-xmark'}"></i> ${msg}`;
  el.className = 'msg ' + type;
}

function setLoad(btnId, spId, txtId, on) {
  document.getElementById(btnId).disabled = on;
  document.getElementById(spId).style.display  = on ? 'block' : 'none';
  document.getElementById(txtId).style.display = on ? 'none'  : 'flex';
}

async function fazerLogin(e) {
  e.preventDefault();
  setLoad('btn-login','sp-login','txt-login', true);
  try {
    const r = await fetch(API+'?action=login', {method:'POST',headers:{'Content-Type':'application/json'},
      body: JSON.stringify({email: document.getElementById('l-email').value.trim(),
                            senha: document.getElementById('l-senha').value})});
    const d = await r.json();
    if (d.ok) { window.location.replace('dashboard.php'); }
    else { showMsg('msg-login', d.msg); setLoad('btn-login','sp-login','txt-login', false); }
  } catch { showMsg('msg-login','Erro ao conectar. Verifique se o servidor PHP está rodando.'); setLoad('btn-login','sp-login','txt-login',false); }
}

async function fazerCadastro(e) {
  e.preventDefault();
  const senha = document.getElementById('c-senha').value;
  const conf  = document.getElementById('c-conf').value;
  if (senha !== conf) { showMsg('msg-cad','Senhas não conferem.'); return; }
  if (senha.length < 6) { showMsg('msg-cad','Senha mínimo 6 caracteres.'); return; }
  setLoad('btn-cad','sp-cad','txt-cad', true);
  try {
    const r = await fetch(API+'?action=cadastro', {method:'POST',headers:{'Content-Type':'application/json'},
      body: JSON.stringify({nome: document.getElementById('c-nome').value.trim(),
                            email: document.getElementById('c-email').value.trim(),
                            senha, confirmar: conf})});
    const d = await r.json();
    if (d.ok) { window.location.replace('dashboard.php'); }
    else { showMsg('msg-cad', d.msg); setLoad('btn-cad','sp-cad','txt-cad', false); }
  } catch { showMsg('msg-cad','Erro ao conectar. Verifique se o servidor PHP está rodando.'); setLoad('btn-cad','sp-cad','txt-cad',false); }
}

async function recuperarSenha(e) {
  e.preventDefault();
  setLoad('btn-rec','sp-rec','txt-rec', true);
  try {
    const r = await fetch(API+'?action=recuperar_senha', {method:'POST',headers:{'Content-Type':'application/json'},
      body: JSON.stringify({email: document.getElementById('r-email').value.trim(),
                            nova_senha: document.getElementById('r-senha').value})});
    const d = await r.json();
    if (d.ok) { showMsg('msg-rec','Senha redefinida! Faça login.','ok'); setTimeout(()=>switchTab('login'),2000); }
    else { showMsg('msg-rec', d.msg); }
  } catch { showMsg('msg-rec','Erro ao conectar.'); }
  setLoad('btn-rec','sp-rec','txt-rec', false);
}
</script>
</body>
</html>
