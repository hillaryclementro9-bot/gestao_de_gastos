P = 'pages/contas.html'
parts = []
parts.append("""<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>PAI PEDE - Cartoes e Contas</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="../css/dashboard.css" />
  <link rel="stylesheet" href="../css/responsivo.css" />
  <script src="../js/velora.js"></script>
  <style>
    .tabs-bar{display:flex;gap:4px;background:#161616;border:1px solid #2a2a2a;border-radius:12px;padding:4px;margin-bottom:24px;width:fit-content}
    .tab-btn{padding:8px 20px;border-radius:9px;border:none;background:transparent;color:#555;font-size:13px;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit}
    .tab-btn.active{background:#e50925;color:#fff;box-shadow:0 4px 14px rgba(229,9,37,.35)}
    .tab-btn:not(.active):hover{color:#ccc;background:#222}
    .tab-pane{display:none}
    .tab-pane.active{display:block}
    .summary-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:28px}
    .sum-card{background:#161616;border:1px solid #222;border-radius:14px;padding:18px 20px;position:relative;overflow:hidden}
    .sum-label{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.6px;margin-bottom:10px}
    .sum-value{font-size:22px;font-weight:800}
    .sum-value.green{color:#4ade80}.sum-value.red{color:#ff354f}.sum-value.yellow{color:#f6c90e}.sum-value.blue{color:#60a5fa}
    .sum-icon{position:absolute;right:16px;top:50%;transform:translateY(-50%);font-size:32px;opacity:.06}
    .cards-section{display:grid;grid-template-columns:1fr 360px;gap:24px;align-items:start}
    .cards-carousel{display:flex;gap:20px;overflow-x:auto;padding-bottom:12px;scroll-snap-type:x mandatory;scrollbar-width:thin;scrollbar-color:#333 transparent}
    .cards-carousel::-webkit-scrollbar{height:4px}
    .cards-carousel::-webkit-scrollbar-thumb{background:#333;border-radius:4px}
    .cc-card{min-width:300px;height:185px;border-radius:20px;padding:22px;position:relative;overflow:hidden;cursor:pointer;flex-shrink:0;scroll-snap-align:start;transition:transform .3s,box-shadow .3s;box-shadow:0 20px 60px rgba(0,0,0,.5)}
    .cc-card:hover{transform:translateY(-6px) scale(1.01);box-shadow:0 30px 80px rgba(0,0,0,.6)}
    .cc-card.selected{outline:2px solid rgba(255,255,255,.35);outline-offset:3px}
    .cc-card.theme-red{background:linear-gradient(135deg,#1a0505 0%,#5c0a14 45%,#1a0505 100%)}
    .cc-card.theme-blue{background:linear-gradient(135deg,#050a1a 0%,#0a2a5c 45%,#050a1a 100%)}
    .cc-card.theme-green{background:linear-gradient(135deg,#051a0a 0%,#0a5c1e 45%,#051a0a 100%)}
    .cc-card.theme-purple{background:linear-gradient(135deg,#0f0518 0%,#3b0a5c 45%,#0f0518 100%)}
    .cc-card.theme-gold{background:linear-gradient(135deg,#1a1205 0%,#5c3d0a 45%,#1a1205 100%)}
    .cc-card.theme-dark{background:linear-gradient(135deg,#0a0a0a 0%,#2a2a2a 45%,#0a0a0a 100%)}
    .cc-shine{position:absolute;inset:0;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.06) 50%,transparent 60%);pointer-events:none}
    .cc-circles{position:absolute;right:-40px;top:-40px;width:200px;height:200px;border-radius:50%;background:rgba(255,255,255,.04);box-shadow:0 0 0 40px rgba(255,255,255,.02)}
    .cc-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px}
    .cc-bank{font-size:13px;font-weight:700;color:rgba(255,255,255,.9);letter-spacing:.5px}
    .cc-brand-icon{font-size:26px;color:rgba(255,255,255,.25)}
    .cc-chip{width:36px;height:28px;border-radius:5px;background:linear-gradient(135deg,#c8960c,#f0d060,#c8960c);margin-bottom:14px;position:relative;overflow:hidden}
    .cc-chip::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(90deg,transparent,transparent 6px,rgba(0,0,0,.15) 6px,rgba(0,0,0,.15) 7px)}
    .cc-number{font-size:13px;letter-spacing:3px;color:rgba(255,255,255,.65);margin-bottom:14px;font-family:monospace}
    .cc-bottom{display:flex;justify-content:space-between;align-items:flex-end}
    .cc-holder-label,.cc-exp-label{font-size:9px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;margin-bottom:3px}
    .cc-exp-label{text-align:right}
    .cc-holder-name,.cc-exp-val{font-size:12px;font-weight:600;color:rgba(255,255,255,.9);text-transform:uppercase;letter-spacing:.5px}
    .card-detail{background:#161616;border:1px solid #222;border-radius:18px;padding:22px;position:sticky;top:20px}
    .cd-header{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px}
    .cd-title{font-size:15px;font-weight:800}
    .cd-actions{display:flex;gap:8px}
    .cd-stat{background:#1e1e1e;border:1px solid #2a2a2a;border-radius:12px;padding:14px 16px;margin-bottom:10px}
    .cd-stat-label{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
    .cd-stat-value{font-size:18px;font-weight:800}
    .cd-limit-track{height:5px;background:#2a2a2a;border-radius:3px;overflow:hidden;margin-top:8px}
    .cd-limit-fill{height:100%;border-radius:3px;transition:width .4s}
    .cd-limit-labels{display:flex;justify-content:space-between;font-size:10px;color:#555;margin-top:4px}
    .cd-tx-title{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px;margin:16px 0 10px}
    .cd-tx-item{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid #1e1e1e}
    .cd-tx-item:last-child{border-bottom:none}
    .cd-tx-icon{width:30px;height:30px;border-radius:8px;display:flex;align-items:center;justify-content:center;font-size:12px;flex-shrink:0}
    .cd-tx-info{flex:1}
    .cd-tx-name{font-size:12px;font-weight:600}
    .cd-tx-date{font-size:10px;color:#555;margin-top:1px}
    .cd-tx-val{font-size:12px;font-weight:700}
    .cd-tx-val.pos{color:#4ade80}.cd-tx-val.neg{color:#ff354f}
    .bank-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:16px}
    .bank-card{background:#161616;border:1px solid #222;border-radius:16px;padding:20px;position:relative;overflow:hidden;transition:all .2s}
    .bank-card::before{content:"";position:absolute;top:0;left:0;right:0;height:3px;background:var(--ac,#e50925)}
    .bank-card:hover{border-color:#333;transform:translateY(-3px);box-shadow:0 12px 40px rgba(0,0,0,.4)}
    .bank-top{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
    .bank-icon{width:44px;height:44px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px}
    .bank-actions{display:flex;gap:6px}
    .bank-name{font-size:15px;font-weight:700;margin-bottom:3px}
    .bank-type{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px}
    .bank-balance{font-size:26px;font-weight:800;margin:14px 0 4px}
    .bank-balance.pos{color:#4ade80}.bank-balance.neg{color:#ff354f}
    .bank-sub{font-size:11px;color:#555}
    .bank-add{background:transparent;border:2px dashed #2a2a2a;border-radius:16px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:all .2s;min-height:160px;color:#444;width:100%;font-family:inherit}
    .bank-add:hover{border-color:#e50925;color:#e50925;background:rgba(229,9,37,.04)}
    .bank-add i{font-size:24px}.bank-add span{font-size:13px;font-weight:600}
    .spend-row{display:flex;align-items:center;gap:12px;margin-bottom:12px}
    .spend-dot{width:10px;height:10px;border-radius:50%;flex-shrink:0}
    .spend-name{flex:1;font-size:13px;color:#ccc}
    .spend-bar-wrap{width:90px;height:5px;background:#2a2a2a;border-radius:3px;overflow:hidden}
    .spend-bar-fill{height:100%;border-radius:3px}
    .spend-val{font-size:12px;font-weight:700;color:#fff;white-space:nowrap;min-width:80px;text-align:right}
    .modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.85);z-index:300;align-items:center;justify-content:center;padding:20px}
    .modal-overlay.open{display:flex}
    .modal-box{background:#181818;border:1px solid #2a2a2a;border-radius:18px;padding:28px;width:100%;max-width:460px;max-height:90vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,.7)}
    .modal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:22px}
    .modal-head h3{font-size:17px;font-weight:800;margin:0}
    .modal-close-btn{width:30px;height:30px;border-radius:7px;border:1px solid #333;background:#222;color:#666;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;transition:all .2s}
    .modal-close-btn:hover{border-color:#e50925;color:#e50925}
    .field{display:flex;flex-direction:column;gap:6px;margin-bottom:14px}
    .field label{font-size:11px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.4px}
    .field input,.field select{background:#222;border:1px solid #333;color:#fff;border-radius:9px;padding:10px 13px;font-size:13px;outline:none;transition:border-color .2s;font-family:inherit}
    .field input::placeholder{color:#555}
    .field input:focus,.field select:focus{border-color:#e50925}
    .modal-foot{display:flex;gap:10px;margin-top:20px}
    .btn-save{flex:1;padding:12px;background:linear-gradient(90deg,#a90019,#ed102f);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
    .btn-save:hover{box-shadow:0 5px 18px rgba(229,9,37,.35);transform:translateY(-1px)}
    .btn-cancel{padding:12px 18px;background:transparent;border:1px solid #333;color:#666;border-radius:9px;font-size:14px;cursor:pointer;transition:all .2s}
    .btn-cancel:hover{border-color:#555;color:#aaa}
    .color-row{display:flex;gap:8px;flex-wrap:wrap}
    .color-dot{width:26px;height:26px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:all .2s}
    .color-dot.selected{border-color:#fff;transform:scale(1.15)}
    @media(max-width:1100px){.cards-section{grid-template-columns:1fr}.card-detail{position:static}}
    @media(max-width:900px){.summary-strip{grid-template-columns:1fr 1fr}}
    @media(max-width:500px){.summary-strip{grid-template-columns:1fr}.cc-card{min-width:270px}}
  </style>
</head>
<body>
""")

parts.append("""<button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
<div class="overlay" id="overlay"></div>
<div class="dashboard">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">PAI <span>PEDE</span></div>
    <nav class="sidebar-menu">
      <a href="dashboard.html"><i class="fa fa-chart-pie"></i> Visao geral</a>
      <a href="transacoes.html"><i class="fa fa-arrow-right-arrow-left"></i> Transacoes</a>
      <a href="contas.html" class="active"><i class="fa fa-credit-card"></i> Cartoes e contas</a>
      <a href="faturas.html"><i class="fa fa-file-invoice-dollar"></i> Faturas</a>
      <a href="planejamento.html"><i class="fa fa-bullseye"></i> Orcamento e metas</a>
      <a href="configuracoes.html"><i class="fa fa-sliders"></i> Categorias e config.</a>
    </nav>
    <div class="sidebar-footer">2025 PAI PEDE</div>
  </aside>
  <main class="main-content">
    <div class="page-header">
      <div><h1>Cartoes e Contas</h1><p>Gerencie seus cartoes de credito e contas bancarias.</p></div>
      <div class="header-right">
        <button class="btn-primary" id="btnNew"><i class="fa fa-plus"></i> Nova conta</button>
        <div class="user-chip"><div class="user-avatar" id="userAvatar">U</div><span class="user-name" id="userName">Usuario</span></div>
      </div>
    </div>
    <div class="summary-strip">
      <div class="sum-card"><div class="sum-label">Saldo total</div><div class="sum-value green" id="sumTotal">R$ 0,00</div><i class="fa fa-wallet sum-icon"></i></div>
      <div class="sum-card"><div class="sum-label">Limite disponivel</div><div class="sum-value yellow" id="sumLimit">R$ 0,00</div><i class="fa fa-credit-card sum-icon"></i></div>
      <div class="sum-card"><div class="sum-label">Fatura atual</div><div class="sum-value red" id="sumFatura">R$ 0,00</div><i class="fa fa-file-invoice sum-icon"></i></div>
      <div class="sum-card"><div class="sum-label">Contas cadastradas</div><div class="sum-value blue" id="sumCount">0</div><i class="fa fa-building-columns sum-icon"></i></div>
    </div>
    <div class="tabs-bar">
      <button class="tab-btn active" data-tab="cartoes"><i class="fa fa-credit-card"></i> Cartoes de Credito</button>
      <button class="tab-btn" data-tab="contas"><i class="fa fa-building-columns"></i> Contas Bancarias</button>
      <button class="tab-btn" data-tab="gastos"><i class="fa fa-chart-bar"></i> Gastos por Conta</button>
    </div>
""")

parts.append("""    <div class="tab-pane active" id="tab-cartoes">
      <div class="cards-section">
        <div>
          <div class="panel" style="margin-bottom:20px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
              <h2 style="margin:0;font-size:15px;font-weight:700"><i class="fa fa-credit-card" style="color:#e50925;margin-right:8px"></i>Meus Cartoes</h2>
              <button class="btn-primary" style="font-size:12px;padding:7px 14px" id="btnNewCard"><i class="fa fa-plus"></i> Novo cartao</button>
            </div>
            <div class="cards-carousel" id="cardsCarousel"></div>
          </div>
          <div class="panel">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
              <h2 style="margin:0;font-size:15px;font-weight:700"><i class="fa fa-clock-rotate-left" style="color:#e50925;margin-right:8px"></i>Ultimas movimentacoes</h2>
              <a href="transacoes.html" style="font-size:12px;color:#555;text-decoration:none">Ver todas</a>
            </div>
            <div id="recentTxList"></div>
          </div>
        </div>
        <div class="card-detail" id="cardDetailPanel">
          <div class="cd-header"><span class="cd-title" id="cdName">Selecione um cartao</span><div class="cd-actions" id="cdActions"></div></div>
          <div id="cdBody"><div style="color:#444;font-size:13px;text-align:center;padding:40px 0"><i class="fa fa-credit-card" style="font-size:36px;display:block;margin-bottom:12px;opacity:.3"></i>Clique em um cartao para ver detalhes</div></div>
        </div>
      </div>
    </div>
    <div class="tab-pane" id="tab-contas">
      <div class="panel">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:18px">
          <h2 style="margin:0;font-size:15px;font-weight:700"><i class="fa fa-building-columns" style="color:#e50925;margin-right:8px"></i>Contas Bancarias</h2>
        </div>
        <div class="bank-grid" id="bankGrid"></div>
      </div>
    </div>
    <div class="tab-pane" id="tab-gastos">
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:20px">
        <div class="panel">
          <h2 style="margin:0 0 18px;font-size:15px;font-weight:700"><i class="fa fa-chart-bar" style="color:#e50925;margin-right:8px"></i>Gastos por conta</h2>
          <div id="spendByAccount"></div>
        </div>
        <div class="panel">
          <h2 style="margin:0 0 18px;font-size:15px;font-weight:700"><i class="fa fa-chart-pie" style="color:#e50925;margin-right:8px"></i>Gastos por categoria</h2>
          <div id="spendByCategory"></div>
        </div>
      </div>
    </div>
  </main>
</div>
""")

parts.append("""<div class="modal-overlay" id="accountModal">
  <div class="modal-box">
    <div class="modal-head"><h3 id="modalTitle">Nova conta</h3><button class="modal-close-btn" id="modalClose"><i class="fa fa-xmark"></i></button></div>
    <div class="field"><label>Nome</label><input type="text" id="aName" placeholder="Ex: Nubank, Bradesco, Carteira..."/></div>
    <div class="field"><label>Tipo</label><select id="aType"><option value="checking">Conta corrente</option><option value="savings">Poupanca</option><option value="credit">Cartao de credito</option><option value="cash">Dinheiro</option><option value="investment">Investimento</option></select></div>
    <div class="field"><label>Saldo inicial (R$)</label><input type="number" id="aBalance" placeholder="0,00" step="0.01"/></div>
    <div class="field" id="limitField"><label>Limite do cartao (R$)</label><input type="number" id="aLimit" placeholder="0,00" step="0.01"/></div>
    <div class="field"><label>Cor</label><div class="color-row" id="colorRow"></div></div>
    <div class="modal-foot"><button class="btn-cancel" id="modalCancel">Cancelar</button><button class="btn-save" id="modalSave"><i class="fa fa-check"></i> Salvar</button></div>
  </div>
</div>
""")

parts.append("""<script>
const fmt=v=>VELORA.fmt(v);
const fmtDate=s=>VELORA.fmtDate(s);
const genId=()=>VELORA.genId();
const COLORS=['#e50925','#3b82f6','#22c55e','#a855f7','#f59e0b','#14b8a6','#f43f5e','#64748b'];
const THEMES={'#e50925':'theme-red','#3b82f6':'theme-blue','#22c55e':'theme-green','#a855f7':'theme-purple','#f59e0b':'theme-gold','#14b8a6':'theme-dark','#f43f5e':'theme-red','#64748b':'theme-dark'};
const ICONS={checking:'fa-building-columns',savings:'fa-piggy-bank',credit:'fa-credit-card',cash:'fa-wallet',investment:'fa-chart-line'};
const LABELS={checking:'Conta corrente',savings:'Poupanca',credit:'Cartao de credito',cash:'Dinheiro',investment:'Investimento'};
let selColor=COLORS[0],editId=null,selCardId=null;

function getAccounts(){return JSON.parse(localStorage.getItem('velora_accounts')||'[]');}
function saveAccounts(arr){localStorage.setItem('velora_accounts',JSON.stringify(arr));VELORA.broadcast();}

(function seed(){
  if(getAccounts().length)return;
  saveAccounts([
    {id:1,name:'Nubank',type:'credit',balance:-850,limit:5000,color:'#a855f7'},
    {id:2,name:'Bradesco',type:'checking',balance:3240.50,limit:0,color:'#e50925'},
    {id:3,name:'Cartao Visa',type:'credit',balance:-1250,limit:8000,color:'#3b82f6'},
    {id:4,name:'Carteira',type:'cash',balance:320,limit:0,color:'#22c55e'},
  ]);
})();

function renderSummary(){
  const accs=getAccounts();
  const total=accs.reduce((s,a)=>s+(a.balance||0),0);
  const creditCards=accs.filter(a=>a.type==='credit');
  const totalLimit=creditCards.reduce((s,a)=>s+(a.limit||0),0);
  const usedCredit=creditCards.reduce((s,a)=>s+Math.abs(Math.min(0,a.balance||0)),0);
  document.getElementById('sumTotal').textContent=fmt(total);
  document.getElementById('sumLimit').textContent=fmt(Math.max(0,totalLimit-usedCredit));
  document.getElementById('sumFatura').textContent=fmt(usedCredit);
  document.getElementById('sumCount').textContent=accs.length;
}

function renderCards(){
  const accs=getAccounts();
  const carousel=document.getElementById('cardsCarousel');
  const creditCards=accs.filter(a=>a.type==='credit');
  if(!creditCards.length){
    carousel.innerHTML='<div style="color:#444;font-size:13px;padding:30px;text-align:center;width:100%"><i class="fa fa-credit-card" style="font-size:32px;display:block;margin-bottom:10px;opacity:.3"></i>Nenhum cartao cadastrado.</div>';
    return;
  }
  const session=VELORA.getSession();
  const holderName=session&&session.name?session.name.split(' ')[0].toUpperCase():'TITULAR';
  carousel.innerHTML=creditCards.map(a=>{
    const theme=THEMES[a.color]||'theme-dark';
    const sel=selCardId===a.id?' selected':'';
    const num='**** **** **** '+String(a.id).padStart(4,'0');
    return `<div class="cc-card ${theme}${sel}" onclick="selectCard(${a.id})">
      <div class="cc-shine"></div><div class="cc-circles"></div>
      <div class="cc-top"><span class="cc-bank">${a.name}</span><i class="fa fa-cc-visa cc-brand-icon"></i></div>
      <div class="cc-chip"></div>
      <div class="cc-number">${num}</div>
      <div class="cc-bottom">
        <div><div class="cc-holder-label">Titular</div><div class="cc-holder-name">${holderName}</div></div>
        <div><div class="cc-exp-label">Validade</div><div class="cc-exp-val">12/28</div></div>
      </div>
    </div>`;
  }).join('');
}

function selectCard(id){
  selCardId=id;
  document.querySelectorAll('.cc-card').forEach(el=>el.classList.remove('selected'));
  const a=getAccounts().find(x=>x.id===id);
  if(!a)return;
  event.currentTarget&&event.currentTarget.classList.add('selected');
  renderCards();
  const txs=VELORA.db.getTransactions().filter(t=>t.account===a.name).slice(-5).reverse();
  const cats=VELORA.db.getCategories();
  const used=Math.abs(Math.min(0,a.balance||0));
  const limit=a.limit||0;
  const pct=limit>0?Math.min(100,Math.round((used/limit)*100)):0;
  const avail=Math.max(0,limit-used);
  document.getElementById('cdName').textContent=a.name;
  document.getElementById('cdActions').innerHTML=`
    <button class="action-btn" onclick="openEdit(${a.id})" title="Editar"><i class="fa fa-pen"></i></button>
    <button class="action-btn del" onclick="deleteAcc(${a.id})" title="Excluir"><i class="fa fa-trash"></i></button>`;
  document.getElementById('cdBody').innerHTML=`
    <div class="cd-stat"><div class="cd-stat-label">Fatura atual</div><div class="cd-stat-value" style="color:#ff354f">${fmt(used)}</div></div>
    <div class="cd-stat"><div class="cd-stat-label">Limite total</div><div class="cd-stat-value">${fmt(limit)}</div>
      <div class="cd-limit-track"><div class="cd-limit-fill" style="width:${pct}%;background:${a.color}"></div></div>
      <div class="cd-limit-labels"><span>Usado: ${pct}%</span><span>Disponivel: ${fmt(avail)}</span></div>
    </div>
    <div class="cd-tx-title">Ultimas transacoes</div>
    ${txs.length?txs.map(t=>{
      const cat=cats.find(c=>c.name===t.category);
      const color=cat?cat.color:'#555';
      const icon=cat?cat.icon:'fa-circle';
      return `<div class="cd-tx-item">
        <div class="cd-tx-icon" style="background:${color}22;color:${color}"><i class="fa ${icon}"></i></div>
        <div class="cd-tx-info"><div class="cd-tx-name">${t.description}</div><div class="cd-tx-date">${fmtDate(t.date)}</div></div>
        <div class="cd-tx-val ${t.type==='income'?'pos':'neg'}">${t.type==='income'?'+':'-'}${fmt(t.amount)}</div>
      </div>`;
    }).join(''):'<div style="color:#444;font-size:12px;padding:12px 0">Nenhuma transacao neste cartao.</div>'}
  `;
}

function renderRecentTx(){
  const txs=VELORA.db.getTransactions();
  const cats=VELORA.db.getCategories();
  const recent=[...txs].sort((a,b)=>b.date>a.date?1:-1).slice(0,6);
  const el=document.getElementById('recentTxList');
  if(!recent.length){el.innerHTML='<div style="color:#444;font-size:13px;padding:20px;text-align:center">Nenhuma movimentacao.</div>';return;}
  el.innerHTML=recent.map(t=>{
    const cat=cats.find(c=>c.name===t.category);
    const color=cat?cat.color:'#555';
    const icon=cat?cat.icon:'fa-circle';
    return `<div class="cd-tx-item">
      <div class="cd-tx-icon" style="background:${color}22;color:${color}"><i class="fa ${icon}"></i></div>
      <div class="cd-tx-info"><div class="cd-tx-name">${t.description}</div><div class="cd-tx-date">${fmtDate(t.date)} &middot; ${t.account||''}</div></div>
      <div class="cd-tx-val ${t.type==='income'?'pos':'neg'}">${t.type==='income'?'+':'-'}${fmt(t.amount)}</div>
    </div>`;
  }).join('');
}

function renderBankGrid(){
  const accs=getAccounts();
  const grid=document.getElementById('bankGrid');
  const nonCredit=accs.filter(a=>a.type!=='credit');
  let html=nonCredit.map(a=>{
    const bal=a.balance||0;
    return `<div class="bank-card" style="--ac:${a.color}">
      <div class="bank-top">
        <div class="bank-icon" style="background:${a.color}22;color:${a.color}"><i class="fa ${ICONS[a.type]||'fa-wallet'}"></i></div>
        <div class="bank-actions">
          <button class="action-btn" onclick="openEdit(${a.id})"><i class="fa fa-pen"></i></button>
          <button class="action-btn del" onclick="deleteAcc(${a.id})"><i class="fa fa-trash"></i></button>
        </div>
      </div>
      <div class="bank-name">${a.name}</div>
      <div class="bank-type">${LABELS[a.type]||a.type}</div>
      <div class="bank-balance ${bal>=0?'pos':'neg'}">${fmt(bal)}</div>
      <div class="bank-sub">Saldo disponivel</div>
    </div>`;
  }).join('');
  html+='<button class="bank-add" onclick="document.getElementById(\'btnNew\').click()"><i class="fa fa-plus"></i><span>Adicionar conta</span></button>';
  grid.innerHTML=html;
}

function renderSpending(){
  const txs=VELORA.db.getTransactions();
  const accs=getAccounts();
  const cats=VELORA.db.getCategories();
  const byAcc={},byCat={};
  txs.filter(t=>t.type==='expense').forEach(t=>{
    byAcc[t.account||'Outros']=(byAcc[t.account||'Outros']||0)+t.amount;
    byCat[t.category||'Outros']=(byCat[t.category||'Outros']||0)+t.amount;
  });
  function buildRows(map,getColor){
    const total=Object.values(map).reduce((s,v)=>s+v,0);
    if(!total)return '<div style="color:#444;font-size:13px;padding:20px;text-align:center">Nenhum gasto registrado.</div>';
    return Object.entries(map).sort((a,b)=>b[1]-a[1]).map(([name,val])=>{
      const pct=Math.round((val/total)*100);
      const color=getColor(name);
      return `<div class="spend-row">
        <div class="spend-dot" style="background:${color}"></div>
        <span class="spend-name">${name}</span>
        <div class="spend-bar-wrap"><div class="spend-bar-fill" style="width:${pct}%;background:${color}"></div></div>
        <span class="spend-val">${fmt(val)}</span>
      </div>`;
    }).join('');
  }
  document.getElementById('spendByAccount').innerHTML=buildRows(byAcc,name=>{const a=accs.find(x=>x.name===name);return a?a.color:'#555';});
  document.getElementById('spendByCategory').innerHTML=buildRows(byCat,name=>{const c=cats.find(x=>x.name===name);return c?c.color:'#555';});
}

function render(){
  renderSummary();renderCards();renderRecentTx();renderBankGrid();renderSpending();
}

document.querySelectorAll('.tab-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-pane').forEach(p=>p.classList.remove('active'));
    btn.classList.add('active');
    document.getElementById('tab-'+btn.dataset.tab).classList.add('active');
  });
});

function buildColorRow(){
  document.getElementById('colorRow').innerHTML=COLORS.map(c=>
    `<div class="color-dot${c===selColor?' selected':''}" data-color="${c}" style="background:${c}" onclick="pickColor('${c}')"></div>`
  ).join('');
}
function pickColor(c){selColor=c;document.querySelectorAll('#colorRow .color-dot').forEach(el=>el.classList.toggle('selected',el.dataset.color===c));}
function openModal(title,acc){
  document.getElementById('modalTitle').textContent=title;
  document.getElementById('aName').value=acc?acc.name:'';
  document.getElementById('aType').value=acc?acc.type:'checking';
  document.getElementById('aBalance').value=acc?acc.balance:'';
  document.getElementById('aLimit').value=acc?acc.limit||'':'';
  selColor=acc?acc.color:COLORS[0];
  buildColorRow();
  document.getElementById('aType').dispatchEvent(new Event('change'));
  document.getElementById('accountModal').classList.add('open');
}
function closeModal(){document.getElementById('accountModal').classList.remove('open');editId=null;}
document.getElementById('aType').addEventListener('change',function(){document.getElementById('limitField').style.display=this.value==='credit'?'flex':'none';});
document.getElementById('btnNew').addEventListener('click',()=>{editId=null;openModal('Nova conta',null);});
document.getElementById('btnNewCard').addEventListener('click',()=>{editId=null;openModal('Novo cartao',{type:'credit',color:COLORS[0]});document.getElementById('aType').value='credit';document.getElementById('aType').dispatchEvent(new Event('change'));});
document.getElementById('modalClose').addEventListener('click',closeModal);
document.getElementById('modalCancel').addEventListener('click',closeModal);
document.getElementById('accountModal').addEventListener('click',e=>{if(e.target===e.currentTarget)closeModal();});
document.getElementById('modalSave').addEventListener('click',()=>{
  const name=document.getElementById('aName').value.trim();
  if(!name){VELORA.showToast('error','Informe o nome da conta.');return;}
  const acc={id:editId||genId(),name,type:document.getElementById('aType').value,balance:parseFloat(document.getElementById('aBalance').value)||0,limit:parseFloat(document.getElementById('aLimit').value)||0,color:selColor};
  let arr=getAccounts();
  if(editId){arr=arr.map(a=>a.id===editId?acc:a);VELORA.showToast('success','Conta atualizada!');}
  else{arr.push(acc);VELORA.showToast('success','Conta criada!');}
  saveAccounts(arr);closeModal();render();
});
function openEdit(id){const a=getAccounts().find(x=>x.id===id);if(!a)return;editId=id;openModal('Editar conta',a);}
function deleteAcc(id){
  if(!confirm('Excluir esta conta?'))return;
  saveAccounts(getAccounts().filter(a=>a.id!==id));
  if(selCardId===id)selCardId=null;
  VELORA.showToast('success','Conta excluida.');render();
}
render();
window.addEventListener('velora:update',render);
window.addEventListener('storage',render);
</script>
</body>
</html>
""")

with open(P,'w',encoding='utf-8') as f:
    f.write(''.join(parts))
print('contas.html written:', len(''.join(parts)), 'chars')
