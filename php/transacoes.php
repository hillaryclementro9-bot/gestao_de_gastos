<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Transações</title>
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
    .btn-logout:hover{background:rgba(229,9,37,.15)}
    .main{margin-left:var(--sidebar);flex:1;padding:32px;min-height:100vh}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:24px;flex-wrap:wrap;gap:12px}
    .topbar h1{font-size:22px;font-weight:800}
    .hamburger{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer}
    .btn-red{padding:10px 20px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;
      display:flex;align-items:center;gap:8px;transition:all .2s}
    .btn-red:hover{opacity:.9;transform:translateY(-1px)}

    /* FILTROS */
    .filtros{display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px}
    .filtros input,.filtros select{background:#1a1a1a;border:1px solid var(--border);border-radius:8px;
      color:#fff;padding:9px 12px;font-size:13px;outline:none;transition:border-color .2s}
    .filtros input:focus,.filtros select:focus{border-color:var(--red)}
    .filtros input{flex:1;min-width:180px}

    /* TABELA */
    .table-wrap{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead th{padding:14px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);
      text-transform:uppercase;letter-spacing:.6px;border-bottom:1px solid var(--border)}
    tbody tr{transition:background .15s}
    tbody tr:hover{background:rgba(255,255,255,.02)}
    tbody td{padding:13px 16px;font-size:13px;border-bottom:1px solid #161616}
    tbody tr:last-child td{border-bottom:none}
    .badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
    .badge.receita{background:rgba(34,197,94,.1);color:#22c55e}
    .badge.despesa{background:rgba(229,9,37,.1);color:var(--red)}
    .badge.pago{background:rgba(34,197,94,.1);color:#22c55e}
    .badge.pendente{background:rgba(245,158,11,.1);color:#f59e0b}
    .badge.cancelado{background:rgba(100,116,139,.1);color:#64748b}
    .val-pos{color:#22c55e;font-weight:700}
    .val-neg{color:var(--red);font-weight:700}
    .btn-icon{background:none;border:none;cursor:pointer;padding:5px 7px;border-radius:6px;
      font-size:13px;transition:all .15s}
    .btn-icon.edit{color:#3b82f6}
    .btn-icon.edit:hover{background:rgba(59,130,246,.1)}
    .btn-icon.del{color:var(--red)}
    .btn-icon.del:hover{background:rgba(229,9,37,.1)}
    .empty-row td{text-align:center;color:#333;padding:40px}

    /* MODAL */
    .modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;
      align-items:center;justify-content:center}
    .modal-bg.open{display:flex}
    .modal{background:#161616;border:1px solid var(--border);border-radius:20px;
      padding:32px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto}
    .modal h2{font-size:18px;font-weight:800;margin-bottom:24px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .group{margin-bottom:16px}
    .group label{display:block;font-size:11px;font-weight:600;color:#444;text-transform:uppercase;
      letter-spacing:.6px;margin-bottom:6px}
    .group input,.group select,.group textarea{width:100%;background:#1a1a1a;border:1px solid #252525;
      border-radius:8px;padding:10px 12px;color:#fff;font-size:13px;outline:none;
      transition:border-color .2s;font-family:inherit}
    .group input:focus,.group select:focus,.group textarea:focus{border-color:var(--red)}
    .group textarea{resize:vertical;min-height:70px}
    .modal-btns{display:flex;gap:10px;justify-content:flex-end;margin-top:8px}
    .btn-cancel{padding:10px 20px;background:transparent;border:1px solid var(--border);
      border-radius:8px;color:#666;font-size:14px;cursor:pointer;transition:all .2s}
    .btn-cancel:hover{border-color:#555;color:#fff}
    .btn-save{padding:10px 24px;background:linear-gradient(135deg,var(--red),var(--red2));
      border:none;border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
    .btn-save:hover{opacity:.9}
    .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99}
    .overlay.open{display:block}
    @media(max-width:900px){
      .sidebar{transform:translateX(-100%)}
      .sidebar.open{transform:translateX(0)}
      .main{margin-left:0}
      .hamburger{display:block}
      .form-row{grid-template-columns:1fr}
    }
    @media(max-width:600px){
      table thead{display:none}
      tbody td{display:block;padding:6px 16px}
      tbody td::before{content:attr(data-label)': ';font-size:10px;color:var(--muted);display:block}
      .main{padding:20px}
    }
  </style>
</head>
<body>
<div class="overlay" id="overlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sb-logo"><div class="logo-hex">P</div><span class="logo-name">PAI PEDE</span></div>
  <nav><ul>
    <li><a href="dashboard.php"><i class="fa fa-chart-pie"></i> Visão geral</a></li>
    <li class="active"><a href="transacoes.php"><i class="fa fa-arrow-right-arrow-left"></i> Transações</a></li>
    <li><a href="contas.php"><i class="fa fa-credit-card"></i> Cartões e contas</a></li>
    <li><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
    <li><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
    <li><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
  </ul></nav>
  <div class="sb-footer">
    <button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
      <h1>Transações</h1>
    </div>
    <button class="btn-red" onclick="abrirModal()"><i class="fa fa-plus"></i> Nova transação</button>
  </div>

  <div class="filtros">
    <input type="text" id="f-busca" placeholder="🔍 Buscar descrição..." oninput="carregar()"/>
    <input type="month" id="f-mes" value="<?= date('Y-m') ?>" onchange="carregar()"/>
    <select id="f-tipo" onchange="carregar()">
      <option value="">Todos os tipos</option>
      <option value="receita">Receita</option>
      <option value="despesa">Despesa</option>
    </select>
    <select id="f-sit" onchange="carregar()">
      <option value="">Todas situações</option>
      <option value="pago">Pago</option>
      <option value="pendente">Pendente</option>
      <option value="cancelado">Cancelado</option>
    </select>
  </div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Descrição</th><th>Categoria</th><th>Tipo</th>
          <th>Data</th><th>Situação</th><th>Valor</th><th>Ações</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
  </div>
</main>

<!-- MODAL -->
<div class="modal-bg" id="modal">
  <div class="modal">
    <h2 id="modal-title">Nova Transação</h2>
    <input type="hidden" id="m-id"/>
    <div class="group">
      <label>Descrição *</label>
      <input type="text" id="m-desc" placeholder="Ex: Supermercado" required/>
    </div>
    <div class="form-row">
      <div class="group">
        <label>Valor *</label>
        <input type="number" id="m-valor" step="0.01" min="0.01" placeholder="0,00" required/>
      </div>
      <div class="group">
        <label>Tipo *</label>
        <select id="m-tipo">
          <option value="despesa">Despesa</option>
          <option value="receita">Receita</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="group">
        <label>Data *</label>
        <input type="date" id="m-data" required/>
      </div>
      <div class="group">
        <label>Vencimento</label>
        <input type="date" id="m-venc"/>
      </div>
    </div>
    <div class="form-row">
      <div class="group">
        <label>Categoria</label>
        <select id="m-cat"><option value="">Sem categoria</option></select>
      </div>
      <div class="group">
        <label>Situação</label>
        <select id="m-sit">
          <option value="pago">Pago</option>
          <option value="pendente">Pendente</option>
          <option value="cancelado">Cancelado</option>
        </select>
      </div>
    </div>
    <div class="form-row">
      <div class="group">
        <label>Conta</label>
        <select id="m-conta"><option value="">Sem conta</option></select>
      </div>
      <div class="group">
        <label>Cartão</label>
        <select id="m-cartao"><option value="">Sem cartão</option></select>
      </div>
    </div>
    <div class="group">
      <label>Observação</label>
      <textarea id="m-obs" placeholder="Opcional..."></textarea>
    </div>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="fecharModal()">Cancelar</button>
      <button class="btn-save" onclick="salvar()"><i class="fa fa-floppy-disk"></i> Salvar</button>
    </div>
  </div>
</div>

<script>
const API = '../php/api.php';

async function carregar() {
  const r = await fetch(API+'?action=get_transacoes', {method:'POST',
    headers:{'Content-Type':'application/json'},
    body: JSON.stringify({
      mes:    document.getElementById('f-mes').value,
      tipo:   document.getElementById('f-tipo').value,
      situacao: document.getElementById('f-sit').value,
      busca:  document.getElementById('f-busca').value
    })});
  const d = await r.json();
  const tb = document.getElementById('tbody');
  if (!d.ok || !d.data.length) {
    tb.innerHTML = '<tr class="empty-row"><td colspan="7">Nenhuma transação encontrada</td></tr>';
    return;
  }
  tb.innerHTML = d.data.map(t => `
    <tr>
      <td data-label="Descrição"><strong>${t.descricao}</strong>${t.conta_nome?'<br><small style="color:#444">'+t.conta_nome+'</small>':''}</td>
      <td data-label="Categoria"><span style="color:${t.categoria_cor||'#666'}">${t.categoria_nome||'—'}</span></td>
      <td data-label="Tipo"><span class="badge ${t.tipo}">${t.tipo}</span></td>
      <td data-label="Data">${fmtData(t.data_transacao)}</td>
      <td data-label="Situação"><span class="badge ${t.situacao}">${t.situacao}</span></td>
      <td data-label="Valor"><span class="${t.tipo==='receita'?'val-pos':'val-neg'}">${t.tipo==='receita'?'+':'-'} ${fmt(t.valor)}</span></td>
      <td>
        <button class="btn-icon edit" onclick='editar(${JSON.stringify(t)})'><i class="fa fa-pen"></i></button>
        <button class="btn-icon del" onclick="excluir(${t.id_transacao})"><i class="fa fa-trash"></i></button>
      </td>
    </tr>`).join('');
}

async function carregarSelects() {
  const [rc, rca] = await Promise.all([
    fetch(API+'?action=get_categorias',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'}),
    fetch(API+'?action=get_contas',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'}),
  ]);
  const cats = (await rc.json()).data || [];
  const contas = (await rca.json()).data || [];

  const sc = document.getElementById('m-cat');
  sc.innerHTML = '<option value="">Sem categoria</option>' +
    cats.map(c=>`<option value="${c.id_categoria}">${c.nome} (${c.tipo})</option>`).join('');

  const sct = document.getElementById('m-conta');
  sct.innerHTML = '<option value="">Sem conta</option>' +
    contas.map(c=>`<option value="${c.id_conta}">${c.nome}</option>`).join('');

  const rcar = await fetch(API+'?action=get_cartoes',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const cartoes = (await rcar.json()).data || [];
  const scar = document.getElementById('m-cartao');
  scar.innerHTML = '<option value="">Sem cartão</option>' +
    cartoes.map(c=>`<option value="${c.id_cartao}">${c.nome}</option>`).join('');
}

function abrirModal(t=null) {
  document.getElementById('m-id').value    = t?.id_transacao || '';
  document.getElementById('m-desc').value  = t?.descricao || '';
  document.getElementById('m-valor').value = t?.valor || '';
  document.getElementById('m-tipo').value  = t?.tipo || 'despesa';
  document.getElementById('m-data').value  = t?.data_transacao || new Date().toISOString().slice(0,10);
  document.getElementById('m-venc').value  = t?.data_vencimento || '';
  document.getElementById('m-sit').value   = t?.situacao || 'pago';
  document.getElementById('m-obs').value   = t?.observacao || '';
  setTimeout(()=>{
    document.getElementById('m-cat').value    = t?.id_categoria || '';
    document.getElementById('m-conta').value  = t?.id_conta || '';
    document.getElementById('m-cartao').value = t?.id_cartao || '';
  }, 50);
  document.getElementById('modal-title').textContent = t ? 'Editar Transação' : 'Nova Transação';
  document.getElementById('modal').classList.add('open');
}

function editar(t) { abrirModal(t); }
function fecharModal() { document.getElementById('modal').classList.remove('open'); }

async function salvar() {
  const body = {
    id_transacao:   document.getElementById('m-id').value || null,
    descricao:      document.getElementById('m-desc').value.trim(),
    valor:          document.getElementById('m-valor').value,
    tipo:           document.getElementById('m-tipo').value,
    data_transacao: document.getElementById('m-data').value,
    data_vencimento:document.getElementById('m-venc').value || null,
    situacao:       document.getElementById('m-sit').value,
    observacao:     document.getElementById('m-obs').value,
    id_categoria:   document.getElementById('m-cat').value || null,
    id_conta:       document.getElementById('m-conta').value || null,
    id_cartao:      document.getElementById('m-cartao').value || null,
  };
  if (!body.descricao || !body.valor || !body.data_transacao) { alert('Preencha os campos obrigatórios.'); return; }
  const r = await fetch(API+'?action=save_transacao',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal(); carregar(); }
  else alert(d.msg);
}

async function excluir(id) {
  if (!confirm('Excluir esta transação?')) return;
  await fetch(API+'?action=delete_transacao',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregar();
}

function fmt(v) { return 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2}); }
function fmtData(d) { if(!d)return'—'; const[y,m,day]=d.split('-'); return`${day}/${m}/${y}`; }
async function logout() { await fetch(API+'?action=logout',{method:'POST'}); window.location.replace('cadastro.php'); }

document.getElementById('hamburger').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});

carregarSelects();
carregar();
</script>
</body>
</html>
