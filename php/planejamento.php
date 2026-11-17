<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Planejamento</title>
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

    /* GRID */
    .grid-2{display:grid;grid-template-columns:1fr 1fr;gap:24px}
    .section-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px}
    .section-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
    .section-header h2{font-size:16px;font-weight:700}
    .btn-sm{padding:7px 14px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;
      border-radius:7px;color:#fff;font-size:12px;font-weight:700;cursor:pointer;
      display:inline-flex;align-items:center;gap:6px}

    /* ORÇAMENTO */
    .orc-item{padding:14px 0;border-bottom:1px solid #161616}
    .orc-item:last-child{border-bottom:none}
    .orc-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
    .orc-name{font-size:13px;font-weight:600}
    .orc-vals{font-size:12px;color:var(--muted)}
    .orc-pct{font-size:12px;font-weight:700}
    .progress{height:8px;background:#1e1e1e;border-radius:4px;overflow:hidden;margin-bottom:4px}
    .progress-bar{height:100%;border-radius:4px;transition:width .5s}
    .orc-actions{display:flex;gap:6px;margin-top:6px}
    .btn-icon{background:none;border:none;cursor:pointer;padding:4px 6px;border-radius:5px;font-size:12px;transition:all .15s}
    .btn-icon.del{color:var(--red)}.btn-icon.del:hover{background:rgba(229,9,37,.1)}

    /* METAS */
    .meta-item{padding:14px 0;border-bottom:1px solid #161616}
    .meta-item:last-child{border-bottom:none}
    .meta-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:8px}
    .meta-name{font-size:14px;font-weight:700}
    .meta-emoji{font-size:20px;margin-right:8px}
    .meta-deadline{font-size:11px;color:#444;margin-top:2px}
    .meta-pct{font-size:13px;font-weight:700;color:var(--red)}
    .meta-vals{display:flex;justify-content:space-between;font-size:12px;color:var(--muted);margin-top:4px}
    .meta-actions{display:flex;gap:6px;margin-top:8px}
    .btn-dep{padding:5px 12px;background:rgba(34,197,94,.1);border:1px solid rgba(34,197,94,.2);
      border-radius:6px;color:#22c55e;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s}
    .btn-dep:hover{background:rgba(34,197,94,.2)}

    /* DICAS */
    .dicas{margin-top:24px;background:rgba(229,9,37,.05);border:1px solid rgba(229,9,37,.15);
      border-radius:12px;padding:20px}
    .dicas h3{font-size:14px;font-weight:700;color:var(--red);margin-bottom:12px}
    .dica-item{display:flex;align-items:flex-start;gap:10px;margin-bottom:10px;font-size:13px;color:#888;line-height:1.5}
    .dica-item:last-child{margin-bottom:0}
    .dica-item i{color:var(--red);margin-top:2px;flex-shrink:0}

    .empty{text-align:center;color:#333;padding:30px;font-size:13px}

    /* MODAL */
    .modal-bg{display:none;position:fixed;inset:0;background:rgba(0,0,0,.7);z-index:200;align-items:center;justify-content:center}
    .modal-bg.open{display:flex}
    .modal{background:#161616;border:1px solid var(--border);border-radius:20px;padding:32px;width:100%;max-width:440px}
    .modal h2{font-size:18px;font-weight:800;margin-bottom:24px}
    .form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
    .group{margin-bottom:16px}
    .group label{display:block;font-size:11px;font-weight:600;color:#444;text-transform:uppercase;letter-spacing:.6px;margin-bottom:6px}
    .group input,.group select,.group textarea{width:100%;background:#1a1a1a;border:1px solid #252525;border-radius:8px;
      padding:10px 12px;color:#fff;font-size:13px;outline:none;transition:border-color .2s;font-family:inherit}
    .group input:focus,.group select:focus{border-color:var(--red)}
    .modal-btns{display:flex;gap:10px;justify-content:flex-end;margin-top:8px}
    .btn-cancel{padding:10px 20px;background:transparent;border:1px solid var(--border);border-radius:8px;color:#666;font-size:14px;cursor:pointer}
    .btn-save{padding:10px 24px;background:linear-gradient(135deg,var(--red),var(--red2));border:none;border-radius:8px;color:#fff;font-size:14px;font-weight:700;cursor:pointer}
    .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99}
    .overlay.open{display:block}
    @media(max-width:1000px){.grid-2{grid-template-columns:1fr}}
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
    <li><a href="contas.php"><i class="fa fa-credit-card"></i> Cartões e contas</a></li>
    <li><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
    <li class="active"><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
    <li><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
  </ul></nav>
  <div class="sb-footer"><button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button></div>
</aside>

<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
      <h1>Planejamento e Metas</h1>
    </div>
    <input type="month" id="mesSel" value="<?= date('Y-m') ?>" onchange="carregarOrcamentos()"
      style="background:#1a1a1a;border:1px solid var(--border);border-radius:8px;color:#fff;padding:9px 12px;font-size:13px;outline:none"/>
  </div>

  <div class="grid-2">
    <!-- ORÇAMENTOS -->
    <div class="section-card">
      <div class="section-header">
        <h2><i class="fa fa-chart-bar" style="color:var(--red)"></i> Orçamentos mensais</h2>
        <button class="btn-sm" onclick="abrirModalOrc()"><i class="fa fa-plus"></i> Novo</button>
      </div>
      <div id="lista-orc"><div class="empty">Nenhum orçamento cadastrado</div></div>
    </div>

    <!-- METAS -->
    <div class="section-card">
      <div class="section-header">
        <h2><i class="fa fa-bullseye" style="color:var(--red)"></i> Metas financeiras</h2>
        <button class="btn-sm" onclick="abrirModalMeta()"><i class="fa fa-plus"></i> Nova</button>
      </div>
      <div id="lista-metas"><div class="empty">Nenhuma meta cadastrada</div></div>
    </div>
  </div>

  <!-- DICAS -->
  <div class="dicas">
    <h3><i class="fa fa-lightbulb"></i> Sugestões para economizar</h3>
    <div class="dica-item"><i class="fa fa-circle-check"></i> Regra 50/30/20: 50% para necessidades, 30% para desejos e 20% para poupança e metas.</div>
    <div class="dica-item"><i class="fa fa-circle-check"></i> Revise assinaturas mensais — serviços que você não usa mais são dinheiro desperdiçado.</div>
    <div class="dica-item"><i class="fa fa-circle-check"></i> Automatize depósitos nas suas metas logo após receber o salário.</div>
    <div class="dica-item"><i class="fa fa-circle-check"></i> Compare preços antes de compras acima de R$ 100 — pequenas economias somam muito.</div>
  </div>
</main>

<!-- MODAL ORÇAMENTO -->
<div class="modal-bg" id="modal-orc">
  <div class="modal">
    <h2 id="mo-title">Novo Orçamento</h2>
    <input type="hidden" id="mo-id"/>
    <div class="group"><label>Categoria</label><select id="mo-cat"><option value="">Geral (sem categoria)</option></select></div>
    <div class="form-row">
      <div class="group"><label>Mês referência *</label><input type="month" id="mo-mes"/></div>
      <div class="group"><label>Limite (R$) *</label><input type="number" id="mo-limite" step="0.01" min="0.01" placeholder="0,00"/></div>
    </div>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="fecharModal('orc')">Cancelar</button>
      <button class="btn-save" onclick="salvarOrc()"><i class="fa fa-floppy-disk"></i> Salvar</button>
    </div>
  </div>
</div>

<!-- MODAL META -->
<div class="modal-bg" id="modal-meta">
  <div class="modal">
    <h2 id="mm-title">Nova Meta</h2>
    <input type="hidden" id="mm-id"/>
    <div class="form-row">
      <div class="group" style="grid-column:span 2"><label>Nome da meta *</label><input type="text" id="mm-nome" placeholder="Ex: Viagem para a praia"/></div>
    </div>
    <div class="form-row">
      <div class="group"><label>Emoji</label><input type="text" id="mm-emoji" value="🎯" maxlength="4"/></div>
      <div class="group"><label>Data limite *</label><input type="date" id="mm-data"/></div>
    </div>
    <div class="form-row">
      <div class="group"><label>Valor alvo (R$) *</label><input type="number" id="mm-alvo" step="0.01" min="0.01" placeholder="0,00"/></div>
      <div class="group"><label>Já acumulado (R$)</label><input type="number" id="mm-acum" step="0.01" value="0"/></div>
    </div>
    <div class="group"><label>Descrição</label><textarea id="mm-desc" placeholder="Opcional..."></textarea></div>
    <div class="modal-btns">
      <button class="btn-cancel" onclick="fecharModal('meta')">Cancelar</button>
      <button class="btn-save" onclick="salvarMeta()"><i class="fa fa-floppy-disk"></i> Salvar</button>
    </div>
  </div>
</div>

<script>
const API = '../php/api.php';

async function carregarOrcamentos() {
  const mes = document.getElementById('mesSel').value;
  const r = await fetch(API+'?action=get_orcamentos',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({mes})});
  const d = await r.json();
  const el = document.getElementById('lista-orc');
  if (!d.data?.length) { el.innerHTML='<div class="empty">Nenhum orçamento para este mês</div>'; return; }
  el.innerHTML = d.data.map(o => {
    const pct = Math.min(100, o.valor_limite > 0 ? (o.gasto/o.valor_limite)*100 : 0);
    const cor = pct < 70 ? '#22c55e' : pct < 90 ? '#f59e0b' : '#e50925';
    return `<div class="orc-item">
      <div class="orc-top">
        <span class="orc-name" style="color:${o.categoria_cor||'#ccc'}">${o.categoria_nome||'Geral'}</span>
        <span class="orc-pct" style="color:${cor}">${Math.round(pct)}%</span>
      </div>
      <div class="progress"><div class="progress-bar" style="width:${pct}%;background:${cor}"></div></div>
      <div class="orc-vals">${fmt(o.gasto)} gasto de ${fmt(o.valor_limite)}</div>
      <div class="orc-actions">
        <button class="btn-icon del" onclick="excluirOrc(${o.id_orcamento})"><i class="fa fa-trash"></i> Excluir</button>
      </div>
    </div>`;
  }).join('');
}

async function carregarMetas() {
  const r = await fetch(API+'?action=get_metas',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const el = document.getElementById('lista-metas');
  if (!d.data?.length) { el.innerHTML='<div class="empty">Nenhuma meta cadastrada</div>'; return; }
  el.innerHTML = d.data.map(m => {
    const pct = Math.min(100, m.valor_alvo > 0 ? (m.valor_acumulado/m.valor_alvo)*100 : 0);
    return `<div class="meta-item">
      <div class="meta-top">
        <div><span class="meta-emoji">${m.emoji}</span><span class="meta-name">${m.nome}</span>
          <div class="meta-deadline">Prazo: ${fmtData(m.data_limite)}</div></div>
        <span class="meta-pct">${Math.round(pct)}%</span>
      </div>
      <div class="progress"><div class="progress-bar" style="width:${pct}%;background:linear-gradient(90deg,var(--red),#ff6b6b)"></div></div>
      <div class="meta-vals"><span>${fmt(m.valor_acumulado)} acumulado</span><span>Meta: ${fmt(m.valor_alvo)}</span></div>
      <div class="meta-actions">
        <button class="btn-dep" onclick="depositar(${m.id_meta})"><i class="fa fa-plus"></i> Depositar</button>
        <button class="btn-icon" style="color:#3b82f6" onclick='abrirModalMeta(${JSON.stringify(m)})'><i class="fa fa-pen"></i></button>
        <button class="btn-icon del" onclick="excluirMeta(${m.id_meta})"><i class="fa fa-trash"></i></button>
      </div>
    </div>`;
  }).join('');
}

async function carregarCats() {
  const r = await fetch(API+'?action=get_categorias',{method:'POST',headers:{'Content-Type':'application/json'},body:'{}'});
  const d = await r.json();
  const s = document.getElementById('mo-cat');
  s.innerHTML = '<option value="">Geral (sem categoria)</option>' +
    (d.data||[]).filter(c=>c.tipo==='despesa').map(c=>`<option value="${c.id_categoria}">${c.nome}</option>`).join('');
}

function abrirModalOrc() {
  document.getElementById('mo-id').value = '';
  document.getElementById('mo-mes').value = document.getElementById('mesSel').value;
  document.getElementById('mo-limite').value = '';
  document.getElementById('modal-orc').classList.add('open');
}

function abrirModalMeta(m=null) {
  document.getElementById('mm-id').value    = m?.id_meta||'';
  document.getElementById('mm-nome').value  = m?.nome||'';
  document.getElementById('mm-emoji').value = m?.emoji||'🎯';
  document.getElementById('mm-data').value  = m?.data_limite||'';
  document.getElementById('mm-alvo').value  = m?.valor_alvo||'';
  document.getElementById('mm-acum').value  = m?.valor_acumulado||0;
  document.getElementById('mm-desc').value  = m?.descricao||'';
  document.getElementById('mm-title').textContent = m ? 'Editar Meta' : 'Nova Meta';
  document.getElementById('modal-meta').classList.add('open');
}

function fecharModal(t) { document.getElementById('modal-'+t).classList.remove('open'); }

async function salvarOrc() {
  const body = {id_orcamento:document.getElementById('mo-id').value||null,
    id_categoria:document.getElementById('mo-cat').value||null,
    mes_referencia:document.getElementById('mo-mes').value,
    valor_limite:document.getElementById('mo-limite').value};
  if (!body.mes_referencia || !body.valor_limite) { alert('Preencha mês e limite.'); return; }
  const r = await fetch(API+'?action=save_orcamento',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal('orc'); carregarOrcamentos(); } else alert(d.msg);
}

async function salvarMeta() {
  const body = {id_meta:document.getElementById('mm-id').value||null,
    nome:document.getElementById('mm-nome').value.trim(),
    emoji:document.getElementById('mm-emoji').value||'🎯',
    data_limite:document.getElementById('mm-data').value,
    valor_alvo:document.getElementById('mm-alvo').value,
    valor_acumulado:document.getElementById('mm-acum').value||0,
    descricao:document.getElementById('mm-desc').value};
  if (!body.nome || !body.data_limite || !body.valor_alvo) { alert('Preencha nome, data e valor alvo.'); return; }
  const r = await fetch(API+'?action=save_meta',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(body)});
  const d = await r.json();
  if (d.ok) { fecharModal('meta'); carregarMetas(); } else alert(d.msg);
}

async function depositar(id) {
  const v = prompt('Valor a depositar (R$):');
  if (!v || isNaN(v) || parseFloat(v) <= 0) return;
  await fetch(API+'?action=depositar_meta',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id,valor:parseFloat(v)})});
  carregarMetas();
}

async function excluirOrc(id) {
  if (!confirm('Excluir este orçamento?')) return;
  await fetch(API+'?action=delete_orcamento',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregarOrcamentos();
}
async function excluirMeta(id) {
  if (!confirm('Excluir esta meta?')) return;
  await fetch(API+'?action=delete_meta',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({id})});
  carregarMetas();
}

function fmt(v){return 'R$ '+parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});}
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

carregarCats();
carregarOrcamentos();
carregarMetas();
</script>
</body>
</html>
