<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Faturas</title>
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
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px;flex-wrap:wrap;gap:12px}
    .topbar h1{font-size:22px;font-weight:800}
    .hamburger{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer}
    .btn-red{padding:10px 20px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;gap:8px;transition:all .2s}
    .btn-red:hover{opacity:.9}

    /* ALERTAS */
    .alertas{margin-bottom:20px}
    .alerta{display:flex;align-items:center;gap:12px;background:rgba(245,158,11,.08);
      border:1px solid rgba(245,158,11,.25);border-radius:10px;padding:12px 16px;
      font-size:13px;color:#f59e0b;margin-bottom:8px}
    .alerta.vencida{background:rgba(229,9,37,.08);border-color:rgba(229,9,37,.25);color:var(--red)}

    /* TABELA */
    .table-wrap{background:var(--card);border:1px solid var(--border);border-radius:16px;overflow:hidden}
    table{width:100%;border-collapse:collapse}
    thead th{padding:14px 16px;text-align:left;font-size:11px;font-weight:600;color:var(--muted);
      text-transform:uppercase;letter-spacing:.6px;border-bottom:1px solid var(--border)}
    tbody tr:hover{background:rgba(255,255,255,.02)}
    tbody td{padding:13px 16px;font-size:13px;border-bottom:1px solid #161616}
    tbody tr:last-child td{border-bottom:none}
    .badge{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
    .badge.aberta{background:rgba(59,130,246,.1);color:#3b82f6}
    .badge.fechada{background:rgba(245,158,11,.1);color:#f59e0b}
    .badge.paga{background:rgba(34,197,94,.1);color:#22c55e}
    .btn-icon{background:none;border:none;cursor:pointer;padding:5px 7px;border-radius:6px;font-size:13px;transition:all .15s}
    .btn-icon.pay{color:#22c55e}.btn-icon.pay:hover{background:rgba(34,197,94,.1)}
    .btn-icon.del{color:var(--red)}.btn-icon.del:hover{background:rgba(229,9,37,.1)}
    .empty-row td{text-align:center;color:#333;padding:40px}

    /* MODAL */
    .modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center}
    .modal-bg.open{display:flex}
    .modal{background:#161616;border:1px solid var(--border);border-radius:20px;padding:32px;width:100%;max-width:460px}
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
    @media(max-width:600px){.main{padding:20px}table thead{display:none}
      tbody td{display:block;padding:6px 16px}tbody td::before{content:attr(data-label)': ';font-size:10px;color:var(--muted);display:block}}
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
    <li class="active"><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
    <li><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
    <li><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
  </ul></nav>
  <div class="sb-footer"><button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button></div>
</aside>

<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
      <h1>Faturas e Contas a Pagar</h1>
    </div>
    <button class="btn-red" onclick="abrirModal()"><i class="fa fa-plus"></i> Nova fatura</button>
  </div>

  <div class="alertas" id="alertas"></div>

  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Cartão</th><th>Competência</th><th>Fechamento</th>
          <th>Vencimento</th><th>Pagamento</th><th>Situação</th><th>Ações</th>
        </tr>
      </thead>
      <tbody id="tbody"></tbody>
    </table>
  </div>
</main>

<!-- MODAL -->
<div class="modal-bg" id="modal">
  <div class="modal">
    <h2 id="modal-title">Nova Fatura</h2>
    <input type="hidden" id="m-id"/>
    <div class="group"><label>Cartão *</label><select id="m-cartao"><option value="">Selecione...</option></select></div>
    <div class="group"><label>Competência (mês) *</label><input type="month" id="m-comp"/></div>
    <div class="form-row">
      <div class="group"><label>Data fechamento</label><input type="date" id="m-fech"/></div>
      <div class="group"><label>Data vencimento</label><input type="date" id="m-venc"/></div>
    </div>
    <div class="form-row">
      <div class="group"><label>Data pagamento</label><input type="date" id="m-pag"/></div>
      <div class="group"><label>Situação</label>
        <select id="m-sit">
          <option value="aberta">Aberta</option>
          <option value="fechada">Fechada</option>
          <option value="paga">Paga</option>
        </select>
      </div>
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
  const r = await fetch(API+'?action=get_faturas',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const tb = document.getElementById('tbody');
  const al = document.getElementById('alertas');
  al.innerHTML = '';

  if (!d.data?.length) { tb.innerHTML='<tr class="empty-row"><td colspan="7">Nenhuma fatura cadastrada</td></tr>'; return; }

  const hoje = new Date(); hoje.setHours(0,0,0,0);
  const alertas = [];

  tb.innerHTML = d.data.map(f => {
    const venc = f.data_vencimento ? new Date(f.data_vencimento+'T00:00:00') : null;
    const diff = venc ? Math.ceil((venc-hoje)/(1000*60*60*24)) : null;
    if (f.situacao !== 'paga' && diff !== null) {
      if (diff < 0) alertas.push({msg:`Fatura ${f.cartao_nome} (${f.competencia}) está VENCIDA há ${Math.abs(diff)} dia(s)!`, tipo:'vencida'});
      else if (diff <= 5) alertas.push({msg:`Fatura ${f.cartao_nome} (${f.competencia}) vence em ${diff} dia(s).`, tipo:'alerta'});
    }
    return `<tr>
      <td data-label="Cartão"><strong>${f.cartao_nome}</strong></td>
      <td data-label="Competência">${f.competencia}</td>
      <td data-label="Fechamento">${fmtData(f.data_fechamento)}</td>
      <td data-label="Vencimento">${fmtData(f.data_vencimento)}${diff!==null&&f.situacao!=='paga'?`<br><small style="color:${diff<0?'var(--red)':'#f59e0b'}">${diff<0?'Vencida':'Vence em '+diff+'d'}</small>`:''}</td>
      <td data-label="Pagamento">${fmtData(f.data_pagamento)||'—'}</td>
      <td data-label="Situação"><span class="badge ${f.situacao}">${f.situacao}</span></td>
      <td>
        ${f.situacao!=='paga'?`<button class="btn-icon pay" onclick="pagar(${f.id_fatura})" title="Registrar pagamento"><i class="fa fa-check"></i></button>`:''}
        <button class="btn-icon del" onclick="excluir(${f.id_fatura})"><i class="fa fa-trash"></i></button>
      </td>
    </tr>`;
  }).join('');

  al.innerHTML = alertas.map(a=>`<div class="alerta ${a.tipo}"><i class="fa fa-triangle-exclamation"></i> ${a.msg}</div>`).join('');
}

async function carregarCartoes() {
  const r = await fetch(API+'?action=get_cartoes',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const s = document.getElementById('m-cartao');
  s.innerHTML = '<option value="">Selecione...</option>' + (d.data||[]).map(c=>`<option value="${c.id_cartao}">${c.nome}</option>`).join('');
}

function abrirModal(f=null) {
  document.getElementById('m-id').value    = f?.id_fatura||'';
  document.getElementById('m-comp').value  = f?.competencia||new Date().toISOString().slice(0,7);
  document.getElementById('m-fech').value  = f?.data_fechamento||'';
  document.getElementById('m-venc').value  = f?.data_vencimento||'';
  document.getElementById('m-pag').value   = f?.data_pagamento||'';
  document.getElementById('m-sit').value   = f?.situacao||'aberta';
  setTimeout(()=>{ document.getElementById('m-cartao').value = f?.id_cartao||''; }, 50);
  document.getElementById('modal-title').textContent = f ? 'Editar Fatura' : 'Nova Fatura';
  document.getElementById('modal').classList.add('open');
}

function fecharModal() { document.getElementById('modal').classList.remove('open'); }

async function salvar() {
  const body = {
    id_fatura: document.getElementById('m-id').value||null,
    id_cartao: document.getElementById('m-cartao').value,
    competencia: document.getElementById('m-comp').value,
    data_fechamento: document.getElementById('m-fech').value||null,
    data_vencimento: document.getElementById('m-venc').value||null,
    data_pagamento: document.getElementById('m-pag').value||null,
    situacao: document.getElementById('m-sit').value
  };
  if (!body.id_cartao || !body.competencia) { alert('Selecione o cartão e a competência.'); return; }
  const r = await fetch(API+'?action=save_fatura',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal(); carregar(); } else alert(d.msg);
}

async function pagar(id) {
  const data = prompt('Data do pagamento (AAAA-MM-DD):', new Date().toISOString().slice(0,10));
  if (!data) return;
  await fetch(API+'?action=pagar_fatura',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id,data_pagamento:data})});
  carregar();
}

async function excluir(id) {
  if (!confirm('Excluir esta fatura?')) return;
  await fetch(API+'?action=delete_fatura',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregar();
}

function fmtData(d){if(!d)return'—';const[y,m,day]=d.split('-');return`${day}/${m}/${y}`;}
async function logout(){await fetch(API+'?action=logout',{method:'POST'});window.location.replace('cadastro.php');}

document.getElementById('hamburger').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay').addEventListener('click',()=>{
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});

carregarCartoes();
carregar();
</script>
</body>
</html>
