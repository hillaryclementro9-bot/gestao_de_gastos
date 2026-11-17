<?php
require_once __DIR__ . '/../php/config.php';
$user = requireLogin();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>PAI PEDE — Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
  <style>
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
    :root{--red:#e50925;--red2:#8b0000;--bg:#0a0a0a;--card:#111;--border:#1e1e1e;--muted:#555;--sidebar:240px}
    body{font-family:'Segoe UI',sans-serif;background:var(--bg);color:#fff;display:flex;min-height:100vh}

    /* SIDEBAR */
    .sidebar{width:var(--sidebar);background:var(--card);border-right:1px solid var(--border);
      display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;
      transition:transform .3s}
    .sb-logo{display:flex;align-items:center;gap:10px;padding:24px 20px 28px}
    .logo-hex{width:36px;height:36px;background:linear-gradient(135deg,var(--red),var(--red2));
      clip-path:polygon(50% 0%,100% 25%,100% 75%,50% 100%,0% 75%,0% 25%);
      display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:900;flex-shrink:0}
    .logo-name{font-size:20px;font-weight:900;letter-spacing:3px}
    nav ul{list-style:none;flex:1}
    nav ul li a{display:flex;align-items:center;gap:12px;padding:12px 20px;color:#666;
      text-decoration:none;font-size:14px;font-weight:500;transition:all .2s;
      border-left:3px solid transparent}
    nav ul li a:hover{color:#fff;background:rgba(229,9,37,.07);border-left-color:var(--red)}
    nav ul li.active a{color:var(--red);background:rgba(229,9,37,.1);border-left-color:var(--red);font-weight:600}
    nav ul li a i{width:18px;text-align:center;font-size:14px}
    .sb-footer{padding:20px;border-top:1px solid var(--border)}
    .sb-user{display:flex;align-items:center;gap:10px;margin-bottom:12px}
    .sb-avatar{width:36px;height:36px;background:linear-gradient(135deg,var(--red),var(--red2));
      border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:14px;font-weight:700}
    .sb-name{font-size:13px;font-weight:600;color:#ccc}
    .sb-email{font-size:11px;color:#444}
    .btn-logout{width:100%;padding:9px;background:rgba(229,9,37,.08);border:1px solid rgba(229,9,37,.2);
      border-radius:8px;color:var(--red);font-size:13px;font-weight:600;cursor:pointer;transition:all .2s}
    .btn-logout:hover{background:rgba(229,9,37,.15)}

    /* MAIN */
    .main{margin-left:var(--sidebar);flex:1;padding:32px;min-height:100vh}
    .topbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:28px}
    .topbar h1{font-size:22px;font-weight:800}
    .topbar-right{display:flex;align-items:center;gap:12px}
    .mes-select{background:#1a1a1a;border:1px solid var(--border);border-radius:8px;
      color:#fff;padding:8px 12px;font-size:13px;cursor:pointer;outline:none}
    .hamburger{display:none;background:none;border:none;color:#fff;font-size:20px;cursor:pointer}

    /* CARDS TOPO */
    .cards-top{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px}
    .card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:22px}
    .card-label{font-size:12px;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:8px}
    .card-value{font-size:28px;font-weight:800}
    .card-value.green{color:#22c55e}
    .card-value.red{color:var(--red)}
    .card-sub{font-size:12px;color:#444;margin-top:4px}
    .card-icon{float:right;width:44px;height:44px;border-radius:12px;display:flex;align-items:center;
      justify-content:center;font-size:18px;margin-top:-4px}
    .card-icon.g{background:rgba(34,197,94,.1);color:#22c55e}
    .card-icon.r{background:rgba(229,9,37,.1);color:var(--red)}
    .card-icon.b{background:rgba(59,130,246,.1);color:#3b82f6}

    /* GRID MEIO */
    .grid-mid{display:grid;grid-template-columns:1fr 320px;gap:16px;margin-bottom:24px}

    /* MAÇÃ */
    .maca-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px;
      display:flex;flex-direction:column;align-items:center}
    .maca-title{font-size:14px;font-weight:700;margin-bottom:4px;align-self:flex-start}
    .maca-sub{font-size:12px;color:var(--muted);margin-bottom:20px;align-self:flex-start}
    .maca-svg-wrap{position:relative;width:160px;height:180px}
    .maca-msg{margin-top:16px;text-align:center;font-size:13px;color:#aaa;line-height:1.5;max-width:240px}
    .maca-pct{font-size:32px;font-weight:900;color:var(--red);margin-top:8px}

    /* GRÁFICO */
    .chart-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px}
    .chart-card h3{font-size:14px;font-weight:700;margin-bottom:4px}
    .chart-card p{font-size:12px;color:var(--muted);margin-bottom:20px}
    .chart-wrap{position:relative;height:220px}

    /* GRID BAIXO */
    .grid-bot{display:grid;grid-template-columns:1fr 1fr;gap:16px}

    /* LISTA */
    .list-card{background:var(--card);border:1px solid var(--border);border-radius:16px;padding:24px}
    .list-card h3{font-size:14px;font-weight:700;margin-bottom:16px}
    .list-item{display:flex;align-items:center;gap:12px;padding:10px 0;border-bottom:1px solid #161616}
    .list-item:last-child{border-bottom:none}
    .li-icon{width:36px;height:36px;border-radius:10px;background:rgba(229,9,37,.1);
      display:flex;align-items:center;justify-content:center;color:var(--red);font-size:13px;flex-shrink:0}
    .li-info{flex:1}
    .li-name{font-size:13px;font-weight:600;color:#ccc}
    .li-date{font-size:11px;color:#444}
    .li-val{font-size:14px;font-weight:700;color:var(--red)}
    .empty{font-size:13px;color:#333;text-align:center;padding:20px 0}

    /* METAS */
    .meta-item{padding:12px 0;border-bottom:1px solid #161616}
    .meta-item:last-child{border-bottom:none}
    .meta-top{display:flex;justify-content:space-between;align-items:center;margin-bottom:6px}
    .meta-name{font-size:13px;font-weight:600}
    .meta-pct{font-size:12px;color:var(--red);font-weight:700}
    .progress{height:6px;background:#1e1e1e;border-radius:3px;overflow:hidden}
    .progress-bar{height:100%;background:linear-gradient(90deg,var(--red),#ff6b6b);border-radius:3px;transition:width .5s}

    /* DICAS */
    .dica{background:rgba(229,9,37,.05);border:1px solid rgba(229,9,37,.15);border-radius:12px;
      padding:14px 16px;margin-top:16px;font-size:13px;color:#aaa;line-height:1.6}
    .dica strong{color:var(--red)}

    /* OVERLAY */
    .overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.6);z-index:99}
    .overlay.open{display:block}

    @media(max-width:1100px){.grid-mid{grid-template-columns:1fr}}
    @media(max-width:900px){
      .sidebar{transform:translateX(-100%)}
      .sidebar.open{transform:translateX(0)}
      .main{margin-left:0}
      .hamburger{display:block}
      .cards-top{grid-template-columns:1fr 1fr}
      .grid-bot{grid-template-columns:1fr}
    }
    @media(max-width:600px){.cards-top{grid-template-columns:1fr}.main{padding:20px}}
  </style>
</head>
<body>

<div class="overlay" id="overlay"></div>

<aside class="sidebar" id="sidebar">
  <div class="sb-logo">
    <div class="logo-hex">P</div>
    <span class="logo-name">PAI PEDE</span>
  </div>
  <nav>
    <ul>
      <li class="active"><a href="dashboard.php"><i class="fa fa-chart-pie"></i> Visão geral</a></li>
      <li><a href="transacoes.php"><i class="fa fa-arrow-right-arrow-left"></i> Transações</a></li>
      <li><a href="contas.php"><i class="fa fa-credit-card"></i> Cartões e contas</a></li>
      <li><a href="faturas.php"><i class="fa fa-file-invoice-dollar"></i> Faturas</a></li>
      <li><a href="planejamento.php"><i class="fa fa-bullseye"></i> Planejamento</a></li>
      <li><a href="configuracoes.php"><i class="fa fa-sliders"></i> Configurações</a></li>
    </ul>
  </nav>
  <div class="sb-footer">
    <div class="sb-user">
      <div class="sb-avatar"><?= strtoupper(substr($user['nome'],0,1)) ?></div>
      <div><div class="sb-name"><?= htmlspecialchars($user['nome']) ?></div>
           <div class="sb-email"><?= htmlspecialchars($user['email']) ?></div></div>
    </div>
    <button class="btn-logout" onclick="logout()"><i class="fa fa-right-from-bracket"></i> Sair</button>
  </div>
</aside>

<main class="main">
  <div class="topbar">
    <div style="display:flex;align-items:center;gap:14px">
      <button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
      <h1>Visão Geral</h1>
    </div>
    <div class="topbar-right">
      <input type="month" id="mesSel" class="mes-select" value="<?= date('Y-m') ?>" onchange="carregarDados()"/>
    </div>
  </div>

  <!-- CARDS TOPO -->
  <div class="cards-top">
    <div class="card">
      <div class="card-icon b"><i class="fa fa-wallet"></i></div>
      <div class="card-label">Saldo disponível</div>
      <div class="card-value" id="val-saldo">—</div>
      <div class="card-sub">Saldo atual</div>
    </div>
    <div class="card">
      <div class="card-icon g"><i class="fa fa-arrow-trend-up"></i></div>
      <div class="card-label">Receitas do mês</div>
      <div class="card-value green" id="val-rec">—</div>
      <div class="card-sub" id="sub-rec">Este mês</div>
    </div>
    <div class="card">
      <div class="card-icon r"><i class="fa fa-arrow-trend-down"></i></div>
      <div class="card-label">Despesas do mês</div>
      <div class="card-value red" id="val-desp">—</div>
      <div class="card-sub" id="sub-desp">Este mês</div>
    </div>
  </div>

  <!-- GRID MEIO -->
  <div class="grid-mid">
    <!-- GRÁFICO -->
    <div class="chart-card">
      <h3>Despesas por categoria</h3>
      <p>Distribuição dos gastos no mês selecionado</p>
      <div class="chart-wrap"><canvas id="chartCat"></canvas></div>
    </div>

    <!-- MAÇÃ FINANCEIRA -->
    <div class="maca-card">
      <div class="maca-title"><i class="fa fa-apple-whole" style="color:var(--red)"></i> Maçã Financeira</div>
      <div class="maca-sub">Indicador da sua saúde financeira</div>
      <div class="maca-svg-wrap">
        <svg id="maca-svg" viewBox="0 0 160 180" width="160" height="180" xmlns="http://www.w3.org/2000/svg">
          <!-- folha -->
          <path d="M80 18 Q95 5 105 15 Q95 28 80 22 Z" fill="#22c55e"/>
          <!-- cabo -->
          <path d="M80 22 Q83 10 88 8" stroke="#22c55e" stroke-width="2.5" fill="none" stroke-linecap="round"/>
          <!-- maçã base -->
          <path id="maca-body" d="M30 70 Q28 40 55 35 Q65 32 80 38 Q95 32 105 35 Q132 40 130 70 Q135 110 110 135 Q95 148 80 148 Q65 148 50 135 Q25 110 30 70 Z" fill="#e50925"/>
          <!-- mordida (clip dinâmico) -->
          <clipPath id="bite-clip">
            <rect id="bite-rect" x="0" y="0" width="0" height="0"/>
          </clipPath>
          <path d="M30 70 Q28 40 55 35 Q65 32 80 38 Q95 32 105 35 Q132 40 130 70 Q135 110 110 135 Q95 148 80 148 Q65 148 50 135 Q25 110 30 70 Z"
                fill="#3a0000" clip-path="url(#bite-clip)" id="maca-bite"/>
          <!-- brilho -->
          <ellipse cx="60" cy="75" rx="14" ry="22" fill="rgba(255,255,255,.18)" transform="rotate(-20,60,75)"/>
        </svg>
      </div>
      <div class="maca-pct" id="maca-pct">—</div>
      <div class="maca-msg" id="maca-msg">Carregando...</div>
    </div>
  </div>

  <!-- GRID BAIXO -->
  <div class="grid-bot">
    <!-- PRÓXIMAS CONTAS -->
    <div class="list-card">
      <h3><i class="fa fa-clock" style="color:var(--red)"></i> Próximas contas</h3>
      <div id="lista-proximas"><div class="empty">Nenhuma conta pendente</div></div>
    </div>

    <!-- METAS -->
    <div class="list-card">
      <h3><i class="fa fa-bullseye" style="color:var(--red)"></i> Metas financeiras</h3>
      <div id="lista-metas"><div class="empty">Nenhuma meta cadastrada</div></div>
      <div class="dica" id="dica-box" style="display:none"></div>
    </div>
  </div>
</main>

<script>
const API = '../php/api.php';
let chartInst = null;

function fmt(v) {
  return 'R$ ' + parseFloat(v||0).toLocaleString('pt-BR',{minimumFractionDigits:2,maximumFractionDigits:2});
}

async function carregarDados() {
  const mes = document.getElementById('mesSel').value;
  try {
    const r = await fetch(API+'?action=dashboard', {method:'POST',
      headers:{'Content-Type':'application/json'}, body: JSON.stringify({mes})});
    const d = await r.json();
    if (!d.ok) return;

    document.getElementById('val-saldo').textContent = fmt(d.saldo);
    document.getElementById('val-rec').textContent   = fmt(d.receitas);
    document.getElementById('val-desp').textContent  = fmt(d.despesas);

    renderChart(d.por_categoria);
    renderMaca(d.receitas, d.despesas);
    renderProximas(d.proximas);
    renderMetas(d.metas);
  } catch(e) { console.error(e); }
}

function renderChart(cats) {
  const ctx = document.getElementById('chartCat').getContext('2d');
  if (chartInst) chartInst.destroy();
  if (!cats || !cats.length) {
    ctx.clearRect(0,0,400,220);
    return;
  }
  chartInst = new Chart(ctx, {
    type: 'doughnut',
    data: {
      labels: cats.map(c => c.nome || 'Sem categoria'),
      datasets: [{
        data: cats.map(c => parseFloat(c.total)),
        backgroundColor: cats.map(c => c.cor || '#e50925'),
        borderWidth: 2, borderColor: '#111'
      }]
    },
    options: {
      responsive: true, maintainAspectRatio: false,
      plugins: {
        legend: { position:'right', labels:{color:'#888',font:{size:12},boxWidth:12,padding:12} },
        tooltip: { callbacks: { label: ctx => ' ' + fmt(ctx.raw) } }
      },
      cutout: '65%'
    }
  });
}

function renderMaca(receitas, despesas) {
  const rec  = parseFloat(receitas || 0);
  const desp = parseFloat(despesas || 0);
  const pct  = rec > 0 ? Math.min((desp / rec) * 100, 100) : (desp > 0 ? 100 : 0);
  const mordida = Math.max(0, pct - 70); // começa a morder acima de 70%
  const proporcao = mordida / 30; // 0 a 1

  document.getElementById('maca-pct').textContent = Math.round(pct) + '% gasto';

  // anima a mordida
  const biteW = proporcao * 90;
  const biteH = proporcao * 80;
  const biteX = 160 - biteW;
  const biteY = 30;
  document.getElementById('bite-rect').setAttribute('x', biteX);
  document.getElementById('bite-rect').setAttribute('y', biteY);
  document.getElementById('bite-rect').setAttribute('width', biteW);
  document.getElementById('bite-rect').setAttribute('height', biteH);

  // cor da maçã
  const body = document.getElementById('maca-body');
  if (pct < 70)       body.setAttribute('fill','#e50925');
  else if (pct < 90)  body.setAttribute('fill','#c0392b');
  else                body.setAttribute('fill','#8b0000');

  // mensagem
  const msg = document.getElementById('maca-msg');
  if (rec === 0 && desp === 0) {
    msg.textContent = 'Nenhuma movimentação neste mês.';
  } else if (pct < 50) {
    msg.innerHTML = '🟢 <strong>Excelente!</strong> Você está economizando bem. Continue assim!';
  } else if (pct < 70) {
    msg.innerHTML = '🟡 <strong>Atenção.</strong> Seus gastos estão crescendo. Fique de olho!';
  } else if (pct < 90) {
    msg.innerHTML = '🟠 <strong>Cuidado!</strong> Gastos altos. Sua maçã está sendo mordida.';
  } else {
    msg.innerHTML = '🔴 <strong>Alerta!</strong> Despesas ultrapassaram as receitas. Revise seus gastos!';
  }
}

function renderProximas(lista) {
  const el = document.getElementById('lista-proximas');
  if (!lista || !lista.length) { el.innerHTML = '<div class="empty">Nenhuma conta pendente</div>'; return; }
  el.innerHTML = lista.map(t => `
    <div class="list-item">
      <div class="li-icon"><i class="fa fa-clock"></i></div>
      <div class="li-info">
        <div class="li-name">${t.descricao}</div>
        <div class="li-date">Vence: ${fmtData(t.data_vencimento)}</div>
      </div>
      <div class="li-val">${fmt(t.valor)}</div>
    </div>`).join('');
}

function renderMetas(metas) {
  const el = document.getElementById('lista-metas');
  const dica = document.getElementById('dica-box');
  if (!metas || !metas.length) { el.innerHTML = '<div class="empty">Nenhuma meta cadastrada</div>'; return; }
  el.innerHTML = metas.map(m => {
    const pct = Math.min(100, (parseFloat(m.valor_acumulado)/parseFloat(m.valor_alvo))*100);
    return `<div class="meta-item">
      <div class="meta-top">
        <span class="meta-name">${m.emoji} ${m.nome}</span>
        <span class="meta-pct">${Math.round(pct)}%</span>
      </div>
      <div class="progress"><div class="progress-bar" style="width:${pct}%"></div></div>
      <div style="font-size:11px;color:#444;margin-top:4px">${fmt(m.valor_acumulado)} de ${fmt(m.valor_alvo)}</div>
    </div>`;
  }).join('');
  dica.style.display = 'block';
  dica.innerHTML = '<strong>💡 Dica:</strong> Separe pelo menos 10% da sua renda mensal para suas metas. Pequenos depósitos regulares fazem grande diferença!';
}

function fmtData(d) {
  if (!d) return '—';
  const [y,m,day] = d.split('-');
  return `${day}/${m}/${y}`;
}

async function logout() {
  await fetch(API+'?action=logout', {method:'POST'});
  window.location.replace('cadastro.php');
}

// hamburger
document.getElementById('hamburger').addEventListener('click', () => {
  document.getElementById('sidebar').classList.toggle('open');
  document.getElementById('overlay').classList.toggle('open');
});
document.getElementById('overlay').addEventListener('click', () => {
  document.getElementById('sidebar').classList.remove('open');
  document.getElementById('overlay').classList.remove('open');
});

carregarDados();
</script>
</body>
</html>
