<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Configurações</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--red:#e50925;--red2:#8b0000;--bg:#0a0a0a;--card:#111;--border:#1e1e1e;--muted:#555;--sidebar:240px}
    body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:#fff;display:flex;min-height:100vh}
    .sidebar{width:var(--sidebar);background:var(--card);border-right:1px solid var(--border);
      display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform .3s}
    .sb-logo{display:flex;align-items:center;gap:10px;padding:24px 20px 28px}
    .logo-hex{width:36px;height:36px;background:linear-gradient(135deg,var(--red),var(--red2));
      clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
      display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;flex-shrink:0}
    .logo-name{font-size:20px;font-weight:900;letter-spacing:3px}
    nav ul{list-style:none}
    nav ul li a{display:flex;align-items:center;gap:12px;padding:12px 20px;color:#666;
      text-decoration:none;font-size:14px;font-weight:500;transition:all .2s;border-left:3px solid transparent}
    nav ul li a:hover{color:#fff;background:rgba(229,9,37,.07);border-left-color:var(--red)}
    nav ul li.active a{color:var(--red);background:rgba(229,9,37,.1);border-left-color:var(--red);font-weight:600}
    nav ul li a i{width:18px;text-align:center;font-size:14px}
    .sb-footer{padding:20px;border-top:1px solid var(--border);margin-top:auto}
    .btn-logout{width:100%;padding:9px;background:rgba(229,9,37,.08);border:1px solid rgba(229,9,37,.2);
      border-radius:8px;color:var(--red);font-size:13px;font-weight:600;cursor:pointer}
    .main{margin-left:var(--sidebar);flex:1;padding:32px;min-height:100vh}
    .topbar{display:flex;align-items:center;gap:14px;margin-bottom:28px}
    .topbar h1{font-size:22px;font-weight:800}
    .hamburger{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer}

    /* GRID */
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
    .section-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:28px;margin-bottom:24px}
    .section-card h2{font-size:16px;font-weight:700;margin-bottom:20px;display:flex;align-items:center;gap:10px}
    .section-card h2 i{color:var(--red)}

    .group{margin-bottom:16px}
    .group label{display:block;font-size:11px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
    .group input,.group select{width:100%;background:#1a1a1a;border:1px solid #252525;border-radius:8px;
      padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:border-color .2s}
    .group input:focus,.group select:focus{border-color:var(--red)}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}

    .btn-save{padding:11px 24px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;gap:8px;transition:all .2s}
    .btn-save:hover{opacity:.9}

    .msg{padding:10px 14px;border-radius:8px;font-size:13px;margin-bottom:14px;display:none;align-items:center;gap:8px}
    .msg.ok{background:rgba(34,197,94,.08);border:1px solid rgba(34,197,94,.25);color:#22c55e;display:flex}
    .msg.err{background:rgba(229,9,37,.08);border:1px solid rgba(229,9,37,.25);color:var(--red);display:flex}

    /* TOGGLE */
    .toggle-row{display:flex;align-items:center;justify-content:space-between;padding:14px 0;border-bottom:1px solid #161616}
    .toggle-row:last-child{border-bottom:none}
    .toggle-info strong{display:block;font-size:14px;margin-bottom:2px}
    .toggle-info span{font-size:12px;color:var(--muted)}
    .toggle{position:relative;width:44px;height:24px;flex-shrink:0}
    .toggle input{opacity:0;width:0;height:0}
    .slider{position:absolute;inset:0;background:#2a2a2a;border-radius:12px;cursor:pointer;transition:.3s}
    .slider::before{content:'';position:absolute;width:18px;height:18px;left:3px;bottom:3px;
      background:#666;border-radius:50%;transition:.3s}
    input:checked+.slider{background:var(--red)}
    input:checked+.slider::before{transform:translateX(20px);background:#fff}

    /* CATEGORIAS */
    .cat-list{display:flex;flex-direction:column;gap:8px;margin-bottom:16px}
    .cat-item{display:flex;align-items:center;gap:10px;background:#1a1a1a;border-radius:10px;padding:10px 14px}
    .cat-dot{width:12px;height:12px;border-radius:50%;flex-shrink:0}
    .cat-name{flex:1;font-size:13px}
    .cat-type{font-size:11px;color:var(--muted)}
    .btn-icon{background:none;border:none;cursor:pointer;padding:4px 6px;border-radius:5px;font-size:12px;transition:all .15s}
    .btn-icon.del{color:var(--red)}.btn-icon.del:hover{background:rgba(229,9,37,.1)}
    .btn-icon.lock{color:#444;cursor:default}

    .add-cat{display:flex;gap:8px;flex-wrap:wrap}
    .add-cat input,.add-cat select{background:#1a1a1a;border:1px solid #252525;border-radius:8px;
      padding:9px 12px;color:#fff;font-size:13px;outline:none;flex:1;min-width:120px}
    .add-cat input:focus,.add-cat select:focus{border-color:var(--red)}
    .btn-sm{padding:9px 16px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:8px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap}

    .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99}
    .overlay.open{display:block}
    @media(max-width:1000px){.grid-2{grid-template-columns:1fr}}
    @media(max-width:900px){
      .sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}
      .main{margin-left:0}.hamburger{display:block}
    }
    @media(max-width:600px){.main{padding:20px}.form-row{grid-template-columns:1fr}}
  </style>
</head>
<body>
<div class="overlay" id="overlay"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-logo"><div class="logo-hex">P</div><span class="logo-name">PAI PEDE</span></div>
  <nav><ul>
    <li><a href="dashboard.php"><i class="fa fa-chart-pie"></i> Visão geral</a></li>
    <li><a href="transacoes.php"><i class="fa fa-arrow-right-arrow-left"></i> Transações</a></li>
    <li><a href="contas.php"><i class="fa fa-credit-card"></i> Cartões e contas</a></li>
    <li><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
    <li><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
    <li class="active"><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
  </ul></nav>
  <div class="sb-footer"><button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button></div>
</aside>

<main class="main">
  <div class="topbar">
    <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
    <h1>Configurações</h1>
  </div>

  <div class="grid-2">
    <!-- PERFIL -->
    <div>
      <div class="section-card">
        <h2><i class="fa fa-user"></i> Editar perfil</h2>
        <div class="msg" id="msg-perfil"></div>
        <div class="group"><label>Nome completo</label><input type="text" id="p-nome"/></div>
        <div class="group"><label>E-mail</label><input type="email" id="p-email"/></div>
        <button class="btn-save" onclick="salvarPerfil()"><i class="fa fa-floppy-disk"></i> Salvar perfil</button>
      </div>

      <div class="section-card">
        <h2><i class="fa fa-lock"></i> Alterar senha</h2>
        <div class="msg" id="msg-senha"></div>
        <div class="group"><label>Senha atual</label><input type="password" id="s-atual" placeholder="Senha atual"/></div>
        <div class="group"><label>Nova senha</label><input type="password" id="s-nova" placeholder="Mínimo 6 caracteres"/></div>
        <div class="group"><label>Confirmar nova senha</label><input type="password" id="s-conf" placeholder="Repita a nova senha"/></div>
        <button class="btn-save" onclick="alterarSenha()"><i class="fa fa-key"></i> Alterar senha</button>
      </div>
    </div>

    <!-- PREFERÊNCIAS + CATEGORIAS -->
    <div>
      <div class="section-card">
        <h2><i class="fa fa-sliders"></i> Preferências</h2>
        <div class="toggle-row">
          <div class="toggle-info"><strong>Notificações</strong><span>Alertas de vencimento e metas</span></div>
          <label class="toggle"><input type="checkbox" id="t-notif" onchange="salvarPrefs()"/><span class="slider"></span></label>
        </div>
        <div class="toggle-row">
          <div class="toggle-info"><strong>Tema escuro</strong><span>Interface em modo escuro</span></div>
          <label class="toggle"><input type="checkbox" id="t-tema" checked onchange="salvarPrefs()"/><span class="slider"></span></label>
        </div>
      </div>

      <div class="section-card">
        <h2><i class="fa fa-tags"></i> Categorias</h2>
        <div class="cat-list" id="cat-list"></div>
        <div class="add-cat">
          <input type="text" id="nc-nome" placeholder="Nome da categoria"/>
          <select id="nc-tipo"><option value="despesa">Despesa</option><option value="receita">Receita</option></select>
          <input type="color" id="nc-cor" value="#e50925" style="width:44px;padding:4px;flex:none"/>
          <button class="btn-sm" onclick="adicionarCat()"><i class="fa fa-plus"></i> Adicionar</button>
        </div>
      </div>
    </div>
  </div>
</main>

<script>
const API = '../php/api.php';

async function carregarPerfil() {
  const r = await fetch(API+'?action=get_perfil',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  if (!d.ok) return;
  document.getElementById('p-nome').value  = d.data.nome;
  document.getElementById('p-email').value = d.data.email;
  document.getElementById('t-notif').checked = d.data.notificacoes == 1;
  document.getElementById('t-tema').checked  = d.data.tema === 'dark';
}

async function salvarPerfil() {
  const body = {nome:document.getElementById('p-nome').value.trim(),
    email:document.getElementById('p-email').value.trim(),
    tema: document.getElementById('t-tema').checked ? 'dark' : 'light',
    notificacoes: document.getElementById('t-notif').checked ? 1 : 0};
  if (!body.nome || !body.email) { showMsg('msg-perfil','Preencha nome e e-mail.','err'); return; }
  const r = await fetch(API+'?action=save_perfil',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  showMsg('msg-perfil', d.ok ? 'Perfil atualizado!' : d.msg, d.ok ? 'ok' : 'err');
}

async function salvarPrefs() {
  const body = {nome:document.getElementById('p-nome').value.trim()||'',
    email:document.getElementById('p-email').value.trim()||'',
    tema: document.getElementById('t-tema').checked ? 'dark' : 'light',
    notificacoes: document.getElementById('t-notif').checked ? 1 : 0};
  await fetch(API+'?action=save_perfil',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
}

async function alterarSenha() {
  const atual = document.getElementById('s-atual').value;
  const nova  = document.getElementById('s-nova').value;
  const conf  = document.getElementById('s-conf').value;
  if (nova !== conf) { showMsg('msg-senha','Senhas não conferem.','err'); return; }
  if (nova.length < 6) { showMsg('msg-senha','Nova senha mínimo 6 caracteres.','err'); return; }
  const r = await fetch(API+'?action=alterar_senha',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({senha_atual:atual,nova_senha:nova})});
  const d = await r.json();
  showMsg('msg-senha', d.ok ? 'Senha alterada com sucesso!' : d.msg, d.ok ? 'ok' : 'err');
  if (d.ok) { document.getElementById('s-atual').value=''; document.getElementById('s-nova').value=''; document.getElementById('s-conf').value=''; }
}

async function carregarCats() {
  const r = await fetch(API+'?action=get_categorias',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const el = document.getElementById('cat-list');
  if (!d.data?.length) { el.innerHTML='<div style="color:#333;font-size:13px">Nenhuma categoria</div>'; return; }
  el.innerHTML = d.data.map(c=>`
    <div class="cat-item">
      <div class="cat-dot" style="background:${c.cor}"></div>
      <span class="cat-name">${c.nome}</span>
      <span class="cat-type">${c.tipo}</span>
      ${c.bloqueada=='1'
        ? '<button class="btn-icon lock" title="Categoria padrão"><i class="fa fa-lock"></i></button>'
        : `<button class="btn-icon del" onclick="excluirCat(${c.id_categoria})"><i class="fa fa-trash"></i></button>`}
    </div>`).join('');
}

async function adicionarCat() {
  const nome = document.getElementById('nc-nome').value.trim();
  const tipo = document.getElementById('nc-tipo').value;
  const cor  = document.getElementById('nc-cor').value;
  if (!nome) { alert('Informe o nome da categoria.'); return; }
  const r = await fetch(API+'?action=save_categoria',{method:'POST',headers:{'Content-Type':'application/json'},
    body:JSON.stringify({nome,tipo,cor,icone:'fa-tag'})});
  const d = await r.json();
  if (d.ok) { document.getElementById('nc-nome').value=''; carregarCats(); } else alert(d.msg);
}

async function excluirCat(id) {
  if (!confirm('Excluir esta categoria?')) return;
  await fetch(API+'?action=delete_categoria',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregarCats();
}

function showMsg(id, msg, type) {
  const el = document.getElementById(id);
  el.innerHTML = `<i class="fa fa-${type==='ok'?'circle-check':'circle-xmark'}"></i> ${msg}`;
  el.className = 'msg ' + type;
  setTimeout(()=>el.className='msg', 4000);
}

async function logout(){await fetch(API+'?action=logout',{method:'POST'});window.location.replace('cadastro.php');}

document.getElementById('hamburger').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});

carregarPerfil();
carregarCats();
</script>
</body>
</html>
