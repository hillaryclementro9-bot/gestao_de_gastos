<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Cartões e Contas</title>
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
      border-radius:8px;color:var(--red);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s}
    .main{margin-left:var(--sidebar);flex:1;padding:32px;min-height:100vh}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
    .topbar h1{font-size:22px;font-weight:800}
    .hamburger{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer}
    .btn-red{padding:10px 20px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;gap:8px;transition:all .2s}
    .btn-red:hover{opacity:.9}

    /* ABAS */
    .tabs{display:flex;gap:0;border-bottom:1px solid var(--border);margin-bottom:24px}
    .tab{padding:12px 24px;font-size:14px;font-weight:600;color:#444;cursor:pointer;
      border-bottom:2px solid transparent;transition:all .2s}
    .tab.active{color:var(--red);border-bottom-color:var(--red)}

    /* GRID CARDS */
    .grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:16px}
    .item-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:22px;
      position:relative;transition:border-color .2s}
    .item-card:hover{border-color:rgba(229,9,37,.3)}
    .item-card-top{display:flex;align-items:center;gap:12px;margin-bottom:16px}
    .item-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;
      justify-content:center;font-size:18px;color:#fff;flex-shrink:0}
    .item-name{font-size:16px;font-weight:700}
    .item-sub{font-size:12px;color:var(--muted)}
    .item-val{font-size:24px;font-weight:800;margin-bottom:4px}
    .item-detail{font-size:12px;color:#444}
    .item-actions{position:absolute;top:16px;right:16px;display:flex;gap:6px}
    .btn-icon{background:none;border:none;cursor:pointer;padding:5px 7px;border-radius:6px;font-size:13px;transition:all .15s}
    .btn-icon.edit{color:#3b82f6}.btn-icon.edit:hover{background:rgba(59,130,246,.1)}
    .btn-icon.del{color:var(--red)}.btn-icon.del:hover{background:rgba(229,9,37,.1)}
    .empty{text-align:center;color:#333;padding:60px 20px;font-size:14px}

    /* CARTÃO VISUAL */
    .card-visual{background:linear-gradient(135deg,#1a1a1a,#2a2a2a);border-radius:16px;
      padding:22px;color:#fff;position:relative;overflow:hidden;min-height:160px}
    .card-visual::before{content:'';position:absolute;width:200px;height:200px;
      border-radius:50%;background:rgba(255,255,255,.04);top:-60px;right:-60px}
    .card-chip{width:36px;height:28px;background:linear-gradient(135deg,#d4a017,#f0c040);
      border-radius:5px;margin-bottom:20px}
    .card-num{font-size:16px;letter-spacing:3px;margin-bottom:16px;color:#ccc}
    .card-bottom{display:flex;justify-content:space-between;align-items:flex-end}
    .card-holder{font-size:13px;font-weight:600;text-transform:uppercase;letter-spacing:1px}
    .card-limit{font-size:12px;color:#888}

    /* MODAL */
    .modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center}
    .modal-bg.open{display:flex}
    .modal{background:#161616;border:1px solid var(--border);border-radius:20px;padding:32px;width:100%;max-width:480px;max-height:90vh;overflow-y:auto}
    .modal h2{font-size:18px;font-weight:800;margin-bottom:24px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .group{margin-bottom:16px}
    .group label{display:block;font-size:11px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
    .group input,.group select{width:100%;background:#1a1a1a;border:1px solid #252525;border-radius:8px;
      padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:border-color .2s}
    .group input:focus,.group select:focus{border-color:var(--red)}
    .modal-btns{display:flex;gap:10px;justify-content:flex-end;margin-top:8px}
    .btn-cancel{padding:10px 20px;background:transparent;border:1px solid var(--border);border-radius:8px;color:#666;font-size:14px;cursor:pointer}
    .btn-save{padding:10px 24px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer}
    .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99}
    .overlay.open{display:block}
    @media(max-width:900px){
      .sidebar{transform:translateX(-100%)}.sidebar.open{transform:translateX(0)}
      .main{margin-left:0}.hamburger{display:block}
    }
    @media(max-width:600px){.main{padding:20px}}
  </style>
</head>
<body>
<div class="overlay" id="overlay"></div>
<aside class="sidebar" id="sidebar">
  <div class="sb-logo"><div class="logo-hex">P</div><span class="logo-name">PAI PEDE</span></div>
  <nav><ul>
    <li><a href="dashboard.php"><i class="fa fa-chart-pie"></i> Visão geral</a></li>
    <li><a href="transacoes.php"><i class="fa fa-arrow-right-arrow-left"></i> Transações</a></li>
    <li class="active"><a href="contas.php"><i class="fa fa-credit-card"></i> Cartões e contas</a></li>
    <li><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
    <li><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
    <li><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
  </ul></nav>
  <div class="sb-footer"><button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button></div>
</aside>

<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
      <h1>Cartões e Contas</h1>
    </div>
    <button class="btn-red" id="btn-novo" onclick="abrirModal()"><i class="fa fa-plus"></i> <span id="btn-novo-txt">Nova conta</span></button>
  </div>

  <div class="tabs">
    <div class="tab active" onclick="switchTab('contas')"><i class="fa fa-building-columns"></i> Contas bancárias</div>
    <div class="tab" onclick="switchTab('cartoes')"><i class="fa fa-credit-card"></i> Cartões</div>
  </div>

  <div id="panel-contas">
    <div class="grid" id="grid-contas"></div>
  </div>
  <div id="panel-cartoes" style="display:none">
    <div class="grid" id="grid-cartoes"></div>
  </div>
</main>

<!-- MODAL CONTA -->
<div class="modal-bg" id="modal-conta">
  <div class="modal">
    <h2 id="mc-title">Nova Conta</h2>
    <input type="hidden" id="mc-id"/>
    <div class="group"><label>Nome da conta *</label><input type="text" id="mc-nome" placeholder="Ex: Nubank"/></div>
    <div class="group"><label>Instituição</label><input type="text" id="mc-inst" placeholder="Ex: Nubank"/></div>
    <div class="form-row">
      <div class="group"><label>Tipo</label>
        <select id="mc-tipo">
          <option value="corrente">Corrente</option>
          <option value="poupanca">Poupança</option>
          <option value="investimento">Investimento</option>
          <option value="outro">Outro</option>
        </select>
      </div>
      <div class="group"><label>Cor</label><input type="color" id="mc-cor" value="#e50925"/></div>
    </div>
    <div class="form-row">
      <div class="group"><label>Saldo inicial</label><input type="number" id="mc-saldo" step="0.01" value="0"/></div>
      <div class="group"><label>Data do saldo</label><input type="date" id="mc-data"/></div>
    </div>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="fecharModal('conta')">Cancelar</button>
      <button class="btn-save" onclick="salvarConta()"><i class="fa fa-floppy-disk"></i> Salvar</button>
    </div>
  </div>
</div>

<!-- MODAL CARTÃO -->
<div class="modal-bg" id="modal-cartao">
  <div class="modal">
    <h2 id="mca-title">Novo Cartão</h2>
    <input type="hidden" id="mca-id"/>
    <div class="group"><label>Nome do cartão *</label><input type="text" id="mca-nome" placeholder="Ex: Visa Gold"/></div>
    <div class="form-row">
      <div class="group"><label>Últimos 4 dígitos</label><input type="text" id="mca-dig" maxlength="4" placeholder="0000"/></div>
      <div class="group"><label>Cor</label><input type="color" id="mca-cor" value="#e50925"/></div>
    </div>
    <div class="group"><label>Limite total</label><input type="number" id="mca-limite" step="0.01" value="0"/></div>
    <div class="form-row">
      <div class="group"><label>Dia fechamento</label><input type="number" id="mca-fech" min="1" max="31" value="1"/></div>
      <div class="group"><label>Dia vencimento</label><input type="number" id="mca-venc" min="1" max="31" value="10"/></div>
    </div>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="fecharModal('cartao')">Cancelar</button>
      <button class="btn-save" onclick="salvarCartao()"><i class="fa fa-floppy-disk"></i> Salvar</button>
    </div>
  </div>
</div>

<script>
const API = '../php/api.php';
let abaAtual = 'contas';

function switchTab(aba) {
  abaAtual = aba;
  document.querySelectorAll('.tab').forEach((t,i)=>t.classList.toggle('active', (i===0&&aba==='contas')||(i===1&&aba==='cartoes')));
  document.getElementById('panel-contas').style.display  = aba==='contas'  ? '' : 'none';
  document.getElementById('panel-cartoes').style.display = aba==='cartoes' ? '' : 'none';
  document.getElementById('btn-novo-txt').textContent = aba==='contas' ? 'Nova conta' : 'Novo cartão';
  if (aba==='contas') carregarContas(); else carregarCartoes();
}

function abrirModal(item=null) {
  if (abaAtual==='contas') {
    document.getElementById('mc-id').value    = item?.id_conta||'';
    document.getElementById('mc-nome').value  = item?.nome||'';
    document.getElementById('mc-inst').value  = item?.instituicao||'';
    document.getElementById('mc-tipo').value  = item?.tipo_conta||'corrente';
    document.getElementById('mc-cor').value   = item?.cor||'#e50925';
    document.getElementById('mc-saldo').value = item?.saldo_inicial||0;
    document.getElementById('mc-data').value  = item?.data_saldo_inicial||'';
    document.getElementById('mc-title').textContent = item ? 'Editar Conta' : 'Nova Conta';
    document.getElementById('modal-conta').classList.add('open');
  } else {
    document.getElementById('mca-id').value     = item?.id_cartao||'';
    document.getElementById('mca-nome').value   = item?.nome||'';
    document.getElementById('mca-dig').value    = item?.ultimos_4||'';
    document.getElementById('mca-cor').value    = item?.cor||'#e50925';
    document.getElementById('mca-limite').value = item?.limite_total||0;
    document.getElementById('mca-fech').value   = item?.dia_fechamento||1;
    document.getElementById('mca-venc').value   = item?.dia_vencimento||10;
    document.getElementById('mca-title').textContent = item ? 'Editar Cartão' : 'Novo Cartão';
    document.getElementById('modal-cartao').classList.add('open');
  }
}

function fecharModal(t) { document.getElementById('modal-'+t).classList.remove('open'); }

async function carregarContas() {
  const r = await fetch(API+'?action=get_contas',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const g = document.getElementById('grid-contas');
  if (!d.data?.length) { g.innerHTML='<div class="empty">Nenhuma conta cadastrada</div>'; return; }
  g.innerHTML = d.data.map(c=>`
    <div class="item-card">
      <div class="item-actions">
        <button class="btn-icon edit" onclick='abrirModal(${JSON.stringify(c)})'><i class="fa fa-pen"></i></button>
        <button class="btn-icon del" onclick="excluirConta(${c.id_conta})"><i class="fa fa-trash"></i></button>
      </div>
      <div class="item-card-top">
        <div class="item-icon" style="background:${c.cor}22;color:${c.cor}"><i class="fa fa-building-columns"></i></div>
        <div><div class="item-name">${c.nome}</div><div class="item-sub">${c.instituicao||c.tipo_conta}</div></div>
      </div>
      <div class="item-val" style="color:${c.cor}">${fmt(c.saldo_inicial)}</div>
      <div class="item-detail">Saldo inicial · ${c.tipo_conta}</div>
    </div>`).join('');
}

async function carregarCartoes() {
  const r = await fetch(API+'?action=get_cartoes',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const g = document.getElementById('grid-cartoes');
  if (!d.data?.length) { g.innerHTML='<div class="empty">Nenhum cartão cadastrado</div>'; return; }
  g.innerHTML = d.data.map(c=>`
    <div class="item-card">
      <div class="item-actions">
        <button class="btn-icon edit" onclick='abrirModal(${JSON.stringify(c)})'><i class="fa fa-pen"></i></button>
        <button class="btn-icon del" onclick="excluirCartao(${c.id_cartao})"><i class="fa fa-trash"></i></button>
      </div>
      <div class="card-visual" style="background:linear-gradient(135deg,${c.cor}33,${c.cor}11);border:1px solid ${c.cor}44">
        <div class="card-chip"></div>
        <div class="card-num">•••• •••• •••• ${c.ultimos_4||'????'}</div>
        <div class="card-bottom">
          <div><div class="card-holder">${c.nome}</div><div class="card-limit">Limite: ${fmt(c.limite_total)}</div></div>
          <div style="text-align:right;font-size:11px;color:#666">Fecha dia ${c.dia_fechamento}<br>Vence dia ${c.dia_vencimento}</div>
        </div>
      </div>
    </div>`).join('');
}

async function salvarConta() {
  const body = {id_conta:document.getElementById('mc-id').value||null,
    nome:document.getElementById('mc-nome').value.trim(),
    instituicao:document.getElementById('mc-inst').value,
    tipo_conta:document.getElementById('mc-tipo').value,
    cor:document.getElementById('mc-cor').value,
    saldo_inicial:document.getElementById('mc-saldo').value,
    data_saldo_inicial:document.getElementById('mc-data').value||null};
  if (!body.nome) { alert('Informe o nome da conta.'); return; }
  const r = await fetch(API+'?action=save_conta',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal('conta'); carregarContas(); } else alert(d.msg);
}

async function salvarCartao() {
  const body = {id_cartao:document.getElementById('mca-id').value||null,
    nome:document.getElementById('mca-nome').value.trim(),
    ultimos_4:document.getElementById('mca-dig').value,
    cor:document.getElementById('mca-cor').value,
    limite_total:document.getElementById('mca-limite').value,
    dia_fechamento:document.getElementById('mca-fech').value,
    dia_vencimento:document.getElementById('mca-venc').value};
  if (!body.nome) { alert('Informe o nome do cartão.'); return; }
  const r = await fetch(API+'?action=save_cartao',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal('cartao'); carregarCartoes(); } else alert(d.msg);
}

async function excluirConta(id) {
  if (!confirm('Excluir esta conta?')) return;
  await fetch(API+'?action=delete_conta',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregarContas();
}
async function excluirCartao(id) {
  if (!confirm('Excluir este cartão?')) return;
  await fetch(API+'?action=delete_cartao',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregarCartoes();
}

function fmt(v){return 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});}
async function logout(){await fetch(API+'?action=logout',{method:'POST'});window.location.replace('cadastro.php');}

document.getElementById('hamburger').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});

carregarContas();
</script>
</body>
</html>
