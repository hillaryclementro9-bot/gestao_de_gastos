P = 'pages/contas.html'
H = []
H.append("""<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8"/>
<meta name="viewport" content="width=device-width,initial-scale=1.0"/>
<title>PAI PEDE - Cartoes e Contas</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
<link rel="stylesheet" href="../css/dashboard.css"/>
<link rel="stylesheet" href="../css/responsivo.css"/>
<script src="../js/velora.js"></script>
<style>
/* ── PAGE LAYOUT ── */
.cc-page{display:flex;flex-direction:column;gap:22px}
.cc-two-col{display:grid;grid-template-columns:1fr 1fr;gap:22px}
.cc-three-col{display:grid;grid-template-columns:repeat(3,1fr);gap:14px;margin-bottom:22px}

/* ── SECTION HEADER ── */
.sec-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:18px;flex-wrap:wrap;gap:10px}
.sec-head h2{font-size:15px;font-weight:700;margin:0;display:flex;align-items:center;gap:8px}
.sec-head h2 i{color:#e50925}

/* ── SUMMARY CARDS ── */
.sum-strip{display:grid;grid-template-columns:repeat(4,1fr);gap:14px;margin-bottom:22px}
.sum-card{background:#161616;border:1px solid #222;border-radius:14px;padding:16px 18px;position:relative;overflow:hidden}
.sum-card::after{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--ac,transparent),transparent 60%);opacity:.06;pointer-events:none}
.sum-lbl{font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px;margin-bottom:8px}
.sum-val{font-size:20px;font-weight:800}
.sum-val.green{color:#4ade80}.sum-val.red{color:#ff354f}.sum-val.yellow{color:#f6c90e}.sum-val.blue{color:#60a5fa}
.sum-ico{position:absolute;right:14px;top:50%;transform:translateY(-50%);font-size:28px;opacity:.07}

/* ── BANK ACCOUNT CARDS ── */
.bank-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px}
.bank-card{background:#161616;border:1px solid #222;border-radius:16px;padding:18px;position:relative;overflow:hidden;transition:all .2s}
.bank-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:var(--ac,#e50925)}
.bank-card:hover{border-color:#333;transform:translateY(-2px);box-shadow:0 10px 32px rgba(0,0,0,.4)}
.bank-card-top{display:flex;align-items:flex-start;justify-content:space-between;margin-bottom:12px}
.bank-card-icon{width:42px;height:42px;border-radius:11px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0}
.bank-card-badge{font-size:10px;font-weight:700;padding:3px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:.4px}
.badge-ativa{background:rgba(74,222,128,.12);color:#4ade80;border:1px solid rgba(74,222,128,.25)}
.badge-inativa{background:rgba(255,53,79,.12);color:#ff354f;border:1px solid rgba(255,53,79,.25)}
.bank-card-name{font-size:15px;font-weight:700;margin-bottom:2px}
.bank-card-inst{font-size:12px;color:#555;margin-bottom:2px}
.bank-card-type{font-size:11px;color:#444;text-transform:uppercase;letter-spacing:.4px}
.bank-card-balance{font-size:24px;font-weight:800;margin:12px 0 4px}
.bank-card-balance.pos{color:#4ade80}.bank-card-balance.neg{color:#ff354f}
.bank-card-actions{display:flex;gap:6px;margin-top:14px;padding-top:12px;border-top:1px solid #1e1e1e}
.bank-card-actions button{flex:1;padding:7px;border-radius:8px;border:1px solid #2a2a2a;background:#1a1a1a;color:#888;font-size:11px;font-weight:600;cursor:pointer;transition:all .2s;display:flex;align-items:center;justify-content:center;gap:5px;font-family:inherit}
.bank-card-actions button:hover{border-color:#e50925;color:#e50925;background:rgba(229,9,37,.06)}
.bank-card-actions button.btn-detail{color:#60a5fa;border-color:rgba(96,165,250,.2)}
.bank-card-actions button.btn-detail:hover{background:rgba(96,165,250,.06);border-color:#60a5fa}
.bank-add-card{background:transparent;border:2px dashed #252525;border-radius:16px;padding:20px;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:10px;cursor:pointer;transition:all .2s;min-height:180px;color:#333;width:100%;font-family:inherit}
.bank-add-card:hover{border-color:#e50925;color:#e50925;background:rgba(229,9,37,.03)}
.bank-add-card i{font-size:26px}.bank-add-card span{font-size:13px;font-weight:600}

/* ── CREDIT CARDS ── */
.cc-carousel{display:flex;gap:18px;overflow-x:auto;padding-bottom:10px;scroll-snap-type:x mandatory;scrollbar-width:thin;scrollbar-color:#2a2a2a transparent}
.cc-carousel::-webkit-scrollbar{height:3px}
.cc-carousel::-webkit-scrollbar-thumb{background:#2a2a2a;border-radius:3px}
.cc-visual{min-width:290px;height:175px;border-radius:18px;padding:20px;position:relative;overflow:hidden;cursor:pointer;flex-shrink:0;scroll-snap-align:start;transition:transform .3s,box-shadow .3s;box-shadow:0 16px 48px rgba(0,0,0,.5)}
.cc-visual:hover{transform:translateY(-5px) scale(1.01);box-shadow:0 24px 64px rgba(0,0,0,.6)}
.cc-visual.sel{outline:2px solid rgba(255,255,255,.4);outline-offset:3px}
.cc-visual.th-red{background:linear-gradient(135deg,#1a0505 0%,#5c0a14 45%,#1a0505 100%)}
.cc-visual.th-blue{background:linear-gradient(135deg,#050a1a 0%,#0a2a5c 45%,#050a1a 100%)}
.cc-visual.th-purple{background:linear-gradient(135deg,#0f0518 0%,#3b0a5c 45%,#0f0518 100%)}
.cc-visual.th-green{background:linear-gradient(135deg,#051a0a 0%,#0a5c1e 45%,#051a0a 100%)}
.cc-visual.th-gold{background:linear-gradient(135deg,#1a1205 0%,#5c3d0a 45%,#1a1205 100%)}
.cc-visual.th-dark{background:linear-gradient(135deg,#0a0a0a 0%,#2a2a2a 45%,#0a0a0a 100%)}
.cc-shine{position:absolute;inset:0;background:linear-gradient(105deg,transparent 40%,rgba(255,255,255,.06) 50%,transparent 60%);pointer-events:none}
.cc-circles{position:absolute;right:-35px;top:-35px;width:180px;height:180px;border-radius:50%;background:rgba(255,255,255,.04);box-shadow:0 0 0 35px rgba(255,255,255,.02)}
.cc-top{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:10px}
.cc-bank-name{font-size:12px;font-weight:700;color:rgba(255,255,255,.9);letter-spacing:.5px}
.cc-brand-ico{font-size:22px;color:rgba(255,255,255,.3)}
.cc-chip{width:32px;height:24px;border-radius:4px;background:linear-gradient(135deg,#c8960c,#f0d060,#c8960c);margin-bottom:10px;position:relative;overflow:hidden}
.cc-chip::after{content:"";position:absolute;inset:0;background:repeating-linear-gradient(90deg,transparent,transparent 5px,rgba(0,0,0,.15) 5px,rgba(0,0,0,.15) 6px)}
.cc-digits{font-size:13px;letter-spacing:3px;color:rgba(255,255,255,.6);margin-bottom:12px;font-family:monospace}
.cc-bottom{display:flex;justify-content:space-between;align-items:flex-end}
.cc-lbl{font-size:8px;color:rgba(255,255,255,.4);text-transform:uppercase;letter-spacing:1px;margin-bottom:2px}
.cc-val{font-size:11px;font-weight:600;color:rgba(255,255,255,.9);text-transform:uppercase}
.cc-limit-bar{position:absolute;bottom:0;left:0;right:0;height:3px;background:rgba(255,255,255,.08)}
.cc-limit-fill{height:100%;transition:width .4s}

/* ── CARD DETAIL PANEL ── */
.detail-panel{background:#161616;border:1px solid #222;border-radius:16px;overflow:hidden}
.detail-tabs{display:flex;border-bottom:1px solid #1e1e1e}
.detail-tab{flex:1;padding:12px;border:none;background:transparent;color:#555;font-size:12px;font-weight:600;cursor:pointer;transition:all .2s;font-family:inherit;border-bottom:2px solid transparent}
.detail-tab.active{color:#fff;border-bottom-color:#e50925;background:rgba(229,9,37,.05)}
.detail-tab:hover:not(.active){color:#aaa;background:rgba(255,255,255,.03)}
.detail-body{padding:18px}
.detail-empty{color:#444;font-size:13px;text-align:center;padding:30px 0}
.detail-empty i{font-size:28px;display:block;margin-bottom:8px;opacity:.4}

/* Resumo tab */
.donut-wrap-sm{display:flex;align-items:center;gap:20px;margin-bottom:16px}
.donut-sm{position:relative;width:100px;height:100px;flex-shrink:0}
.donut-sm svg{transform:rotate(-90deg)}
.donut-sm-center{position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center}
.donut-sm-pct{font-size:16px;font-weight:800}
.donut-sm-lbl{font-size:9px;color:#555;text-transform:uppercase}
.detail-stat-row{display:flex;justify-content:space-between;align-items:center;padding:8px 0;border-bottom:1px solid #1e1e1e;font-size:12px}
.detail-stat-row:last-child{border-bottom:none}
.detail-stat-lbl{color:#666}
.detail-stat-val{font-weight:700}

/* Faturas tab */
.fatura-row{display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid #1e1e1e;font-size:12px}
.fatura-row:last-child{border-bottom:none}
.fatura-comp{flex:1;font-weight:600}
.fatura-venc{color:#666;min-width:70px}
.fatura-val{font-weight:700;min-width:80px;text-align:right}
.fatura-badge{font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;text-transform:uppercase;white-space:nowrap}
.fb-paga{background:rgba(74,222,128,.12);color:#4ade80}
.fb-pendente{background:rgba(255,53,79,.12);color:#ff354f}
.fb-vencida{background:rgba(249,115,22,.12);color:#f97316}

/* Transacoes tab */
.tx-row{display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid #1e1e1e}
.tx-row:last-child{border-bottom:none}
.tx-icon{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;font-size:11px;flex-shrink:0}
.tx-info{flex:1;min-width:0}
.tx-desc{font-size:12px;font-weight:600;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.tx-cat{font-size:10px;color:#555;margin-top:1px}
.tx-date{font-size:10px;color:#555;white-space:nowrap}
.tx-val{font-size:12px;font-weight:700;white-space:nowrap}
.tx-val.neg{color:#ff354f}.tx-val.pos{color:#4ade80}

/* ── PROXIMAS FATURAS ── */
.fat-list{display:flex;flex-direction:column;gap:8px}
.fat-item{display:flex;align-items:center;gap:12px;background:#1a1a1a;border:1px solid #222;border-radius:10px;padding:11px 14px;transition:border-color .2s}
.fat-item:hover{border-color:#2a2a2a}
.fat-item.fat-paga{border-left:3px solid #4ade80}
.fat-item.fat-pendente{border-left:3px solid #ff354f}
.fat-item.fat-vencida{border-left:3px solid #f97316}
.fat-card-name{font-size:13px;font-weight:600;flex:1}
.fat-comp{font-size:11px;color:#555;min-width:55px}
.fat-venc{font-size:11px;color:#666;min-width:75px}
.fat-val{font-size:13px;font-weight:700;min-width:90px;text-align:right}

/* ── DICA ── */
.dica-card{background:linear-gradient(135deg,#161616,#1a0a0d);border:1px solid #2a1a1f;border-radius:16px;padding:20px;display:flex;align-items:center;gap:18px}
.dica-icon{width:48px;height:48px;border-radius:12px;background:rgba(229,9,37,.12);border:1px solid rgba(229,9,37,.2);display:flex;align-items:center;justify-content:center;font-size:22px;color:#e50925;flex-shrink:0}
.dica-text p{font-size:13px;color:#888;line-height:1.6;margin:0}
.dica-text strong{color:#ccc}

/* ── MODALS ── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.88);z-index:400;align-items:center;justify-content:center;padding:20px;backdrop-filter:blur(4px)}
.modal-overlay.open{display:flex}
.modal-box{background:#181818;border:1px solid #2a2a2a;border-radius:18px;padding:26px;width:100%;max-width:480px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,.7)}
.modal-head{display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
.modal-head h3{font-size:16px;font-weight:800;margin:0}
.modal-close{width:30px;height:30px;border-radius:7px;border:1px solid #333;background:#222;color:#666;cursor:pointer;font-size:13px;display:flex;align-items:center;justify-content:center;transition:all .2s}
.modal-close:hover{border-color:#e50925;color:#e50925}
.field{display:flex;flex-direction:column;gap:5px;margin-bottom:13px}
.field label{font-size:11px;font-weight:600;color:#555;text-transform:uppercase;letter-spacing:.4px}
.field input,.field select{background:#222;border:1px solid #2e2e2e;color:#fff;border-radius:9px;padding:10px 12px;font-size:13px;outline:none;transition:border-color .2s;font-family:inherit}
.field input::placeholder{color:#444}
.field input:focus,.field select:focus{border-color:#e50925}
.two-col-fields{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.modal-foot{display:flex;gap:10px;margin-top:18px}
.btn-save{flex:1;padding:11px;background:linear-gradient(90deg,#a90019,#ed102f);color:#fff;border:none;border-radius:9px;font-size:14px;font-weight:700;cursor:pointer;transition:all .2s}
.btn-save:hover{box-shadow:0 5px 18px rgba(229,9,37,.35);transform:translateY(-1px)}
.btn-cancel{padding:11px 16px;background:transparent;border:1px solid #333;color:#666;border-radius:9px;font-size:14px;cursor:pointer;transition:all .2s}
.btn-cancel:hover{border-color:#555;color:#aaa}
.color-row{display:flex;gap:7px;flex-wrap:wrap}
.color-dot{width:24px;height:24px;border-radius:50%;cursor:pointer;border:3px solid transparent;transition:all .2s}
.color-dot.sel{border-color:#fff;transform:scale(1.15)}
.brand-row{display:flex;gap:7px;flex-wrap:wrap}
.brand-btn{padding:6px 12px;border-radius:8px;border:1px solid #2e2e2e;background:#222;color:#666;font-size:12px;cursor:pointer;transition:all .2s;font-family:inherit;display:flex;align-items:center;gap:5px}
.brand-btn.sel{border-color:#e50925;color:#fff;background:#2a1015}
.brand-btn i{font-size:15px}

/* ── DETAIL MODAL (conta) ── */
.detail-modal-box{background:#181818;border:1px solid #2a2a2a;border-radius:18px;padding:26px;width:100%;max-width:520px;max-height:92vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,.7)}
.dm-stat{background:#1e1e1e;border:1px solid #252525;border-radius:10px;padding:12px 14px;margin-bottom:10px}
.dm-stat-lbl{font-size:10px;color:#555;text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px}
.dm-stat-val{font-size:18px;font-weight:800}

@media(max-width:1100px){.cc-two-col{grid-template-columns:1fr}}
@media(max-width:900px){.sum-strip{grid-template-columns:1fr 1fr}.cc-three-col{grid-template-columns:1fr 1fr}}
@media(max-width:500px){.sum-strip{grid-template-columns:1fr}.cc-three-col{grid-template-columns:1fr}.two-col-fields{grid-template-columns:1fr}}
</style>
</head>
<body>
""")

H.append("""<button class="hamburger" id="hamburger"><i class="fa fa-bars"></i></button>
<div class="overlay" id="overlay"></div>
<div class="dashboard">
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-logo">PAI <span>PEDE</span></div>
    <nav class="sidebar-menu">
      <a href="dashboard.html"><i class="fa fa-chart-pie"></i> Visao geral</a>
      <a href="transacoes.html"><i class="fa fa-arrow-right-arrow-left"></i> Transacoes</a>
      <a href="contas.html" class="active"><i class="fa fa-credit-card"></i> Cartoes e contas</a>
      <a href="faturas.html"><i class="fa fa-file-invoice-dollar"></i> Faturas</a>
      <a href="planejamento.html"><i class="fa fa-bullseye"></i> Planejamento e metas</a>
      <a href="configuracoes.html"><i class="fa fa-sliders"></i> Categorias e config.</a>
    </nav>
    <div class="sidebar-footer">2025 PAI PEDE</div>
  </aside>
  <main class="main-content">
    <div class="page-header">
      <div><h1>Cartoes e Contas</h1><p>Gerencie suas contas bancarias e cartoes de credito.</p></div>
      <div class="header-right">
        <div class="user-chip"><div class="user-avatar" id="userAvatar">U</div><span class="user-name" id="userName">Usuario</span></div>
      </div>
    </div>
    <div class="cc-page">

      <!-- SUMMARY STRIP -->
      <div class="sum-strip">
        <div class="sum-card"><div class="sum-lbl">Saldo total</div><div class="sum-val green" id="sumSaldo">R$ 0,00</div><i class="fa fa-wallet sum-ico"></i></div>
        <div class="sum-card"><div class="sum-lbl">Limite disponivel</div><div class="sum-val yellow" id="sumLimite">R$ 0,00</div><i class="fa fa-credit-card sum-ico"></i></div>
        <div class="sum-card"><div class="sum-lbl">Fatura em aberto</div><div class="sum-val red" id="sumFatura">R$ 0,00</div><i class="fa fa-file-invoice sum-ico"></i></div>
        <div class="sum-card"><div class="sum-lbl">Contas ativas</div><div class="sum-val blue" id="sumContas">0</div><i class="fa fa-building-columns sum-ico"></i></div>
      </div>

      <!-- ROW 1: CONTAS + CARTOES -->
      <div class="cc-two-col">

        <!-- CONTAS BANCARIAS -->
        <div class="panel">
          <div class="sec-head">
            <h2><i class="fa fa-building-columns"></i> Minhas Contas Bancarias</h2>
            <button class="btn-primary" style="font-size:12px;padding:7px 14px" id="btnAddConta"><i class="fa fa-plus"></i> Adicionar conta</button>
          </div>
          <div class="bank-grid" id="bankGrid"></div>
        </div>

        <!-- CARTOES -->
        <div class="panel">
          <div class="sec-head">
            <h2><i class="fa fa-credit-card"></i> Meus Cartoes</h2>
            <button class="btn-primary" style="font-size:12px;padding:7px 14px" id="btnAddCard"><i class="fa fa-plus"></i> Adicionar cartao</button>
          </div>
          <div class="cc-carousel" id="ccCarousel"></div>
        </div>
      </div>

      <!-- ROW 2: DETALHES DO CARTAO -->
      <div class="panel" id="cardDetailSection">
        <div class="sec-head">
          <h2><i class="fa fa-chart-pie"></i> Detalhes do Cartao</h2>
          <span id="detailCardName" style="font-size:12px;color:#555">Selecione um cartao acima</span>
        </div>
        <div class="detail-panel">
          <div class="detail-tabs">
            <button class="detail-tab active" data-tab="resumo">Resumo</button>
            <button class="detail-tab" data-tab="faturas">Faturas</button>
            <button class="detail-tab" data-tab="transacoes">Transacoes</button>
          </div>
          <div class="detail-body" id="detailBody">
            <div class="detail-empty"><i class="fa fa-credit-card"></i>Clique em um cartao para ver os detalhes</div>
          </div>
        </div>
      </div>

      <!-- ROW 3: PROXIMAS FATURAS -->
      <div class="panel">
        <div class="sec-head">
          <h2><i class="fa fa-file-invoice-dollar"></i> Proximas Faturas</h2>
          <a href="faturas.html" style="font-size:12px;color:#555;text-decoration:none">Ver todas <i class="fa fa-arrow-right"></i></a>
        </div>
        <div class="fat-list" id="fatList"></div>
      </div>

      <!-- DICA -->
      <div class="dica-card">
        <div class="dica-icon"><i class="fa fa-lightbulb"></i></div>
        <div class="dica-text">
          <strong>Dica do Pai Pede!</strong>
          <p>Mantenha seus gastos dentro do planejado e evite o uso excessivo do cartao. Assim, voce mantem sua saude financeira sempre em dia!</p>
        </div>
      </div>

    </div>
  </main>
</div>
""")

H.append("""<!-- MODAL CONTA -->
<div class="modal-overlay" id="modalConta">
  <div class="modal-box">
    <div class="modal-head"><h3 id="modalContaTitulo">Nova Conta</h3><button class="modal-close" id="modalContaClose"><i class="fa fa-xmark"></i></button></div>
    <div class="field"><label>Nome da conta</label><input type="text" id="cNome" placeholder="Ex: Conta corrente, Poupanca..."/></div>
    <div class="field"><label>Instituicao financeira</label><input type="text" id="cInst" placeholder="Ex: Nubank, Bradesco, Itau..."/></div>
    <div class="field"><label>Tipo de conta</label>
      <select id="cTipo">
        <option value="corrente">Conta corrente</option>
        <option value="poupanca">Poupanca</option>
        <option value="investimento">Investimento</option>
        <option value="dinheiro">Dinheiro</option>
        <option value="outro">Outro</option>
      </select>
    </div>
    <div class="two-col-fields">
      <div class="field"><label>Saldo inicial (R$)</label><input type="number" id="cSaldo" placeholder="0,00" step="0.01"/></div>
      <div class="field"><label>Data do saldo inicial</label><input type="date" id="cData"/></div>
    </div>
    <div class="field"><label>Situacao</label>
      <select id="cSituacao"><option value="ativa">Ativa</option><option value="inativa">Inativa</option></select>
    </div>
    <div class="field"><label>Cor</label><div class="color-row" id="contaColorRow"></div></div>
    <div class="modal-foot"><button class="btn-cancel" id="modalContaCancel">Cancelar</button><button class="btn-save" id="modalContaSave"><i class="fa fa-check"></i> Salvar</button></div>
  </div>
</div>

<!-- MODAL CARTAO -->
<div class="modal-overlay" id="modalCartao">
  <div class="modal-box">
    <div class="modal-head"><h3 id="modalCartaoTitulo">Novo Cartao</h3><button class="modal-close" id="modalCartaoClose"><i class="fa fa-xmark"></i></button></div>
    <div class="field"><label>Nome do cartao</label><input type="text" id="ccNome" placeholder="Ex: Nubank, Visa Bradesco..."/></div>
    <div class="field"><label>Conta bancaria vinculada</label><select id="ccConta"><option value="">Nenhuma</option></select></div>
    <div class="field"><label>Bandeira</label>
      <div class="brand-row" id="brandRow">
        <button class="brand-btn sel" data-brand="visa"><i class="fa-brands fa-cc-visa"></i> Visa</button>
        <button class="brand-btn" data-brand="mastercard"><i class="fa-brands fa-cc-mastercard"></i> Mastercard</button>
        <button class="brand-btn" data-brand="amex"><i class="fa-brands fa-cc-amex"></i> Amex</button>
        <button class="brand-btn" data-brand="elo"><i class="fa fa-credit-card"></i> Elo</button>
      </div>
    </div>
    <div class="two-col-fields">
      <div class="field"><label>Ultimos 4 digitos</label><input type="text" id="ccDigits" placeholder="0000" maxlength="4"/></div>
      <div class="field"><label>Limite total (R$)</label><input type="number" id="ccLimite" placeholder="0,00" step="0.01"/></div>
    </div>
    <div class="two-col-fields">
      <div class="field"><label>Dia de fechamento</label><input type="number" id="ccFecha" placeholder="1-31" min="1" max="31"/></div>
      <div class="field"><label>Dia de vencimento</label><input type="number" id="ccVence" placeholder="1-31" min="1" max="31"/></div>
    </div>
    <div class="field"><label>Cor do cartao</label><div class="color-row" id="cartaoColorRow"></div></div>
    <div class="modal-foot"><button class="btn-cancel" id="modalCartaoCancel">Cancelar</button><button class="btn-save" id="modalCartaoSave"><i class="fa fa-check"></i> Salvar</button></div>
  </div>
</div>

<!-- MODAL DETALHE CONTA -->
<div class="modal-overlay" id="modalDetalheConta">
  <div class="detail-modal-box">
    <div class="modal-head"><h3 id="detalheContaTitulo">Detalhes da Conta</h3><button class="modal-close" id="modalDetalheContaClose"><i class="fa fa-xmark"></i></button></div>
    <div id="detalheContaBody"></div>
  </div>
</div>
""")

H.append("""<script>
const fmt=v=>VELORA.fmt(v);
const fmtDate=s=>VELORA.fmtDate(s);
const genId=()=>VELORA.genId();
const COLORS=['#e50925','#a855f7','#3b82f6','#22c55e','#f59e0b','#14b8a6','#f43f5e','#64748b'];
const THEMES={
  '#e50925':'th-red','#a855f7':'th-purple','#3b82f6':'th-blue',
  '#22c55e':'th-green','#f59e0b':'th-gold','#14b8a6':'th-dark',
  '#f43f5e':'th-red','#64748b':'th-dark'
};
const BRAND_ICONS={visa:'fa-brands fa-cc-visa',mastercard:'fa-brands fa-cc-mastercard',amex:'fa-brands fa-cc-amex',elo:'fa fa-credit-card'};
const TIPO_LABELS={corrente:'Conta corrente',poupanca:'Poupanca',investimento:'Investimento',dinheiro:'Dinheiro',outro:'Outro'};
const TIPO_ICONS={corrente:'fa-building-columns',poupanca:'fa-piggy-bank',investimento:'fa-chart-line',dinheiro:'fa-wallet',outro:'fa-circle-dot'};

let selContaColor=COLORS[0], selCartaoColor=COLORS[0], selBrand='visa';
let editContaId=null, editCartaoId=null, selCardId=null, activeTab='resumo';

// ── STORAGE ──
function getContas(){return JSON.parse(localStorage.getItem('pp_contas')||'[]');}
function saveContas(a){localStorage.setItem('pp_contas',JSON.stringify(a));VELORA.broadcast();}
function getCartoes(){return JSON.parse(localStorage.getItem('pp_cartoes')||'[]');}
function saveCartoes(a){localStorage.setItem('pp_cartoes',JSON.stringify(a));VELORA.broadcast();}
function getFaturas(){return JSON.parse(localStorage.getItem('pp_faturas')||'[]');}
function saveFaturas(a){localStorage.setItem('pp_faturas',JSON.stringify(a));VELORA.broadcast();}

// ── SALDO CALCULADO ──
function calcSaldo(conta){
  const txs=VELORA.db.getTransactions();
  const mov=txs.filter(t=>t.account===conta.nome&&t.status==='paid');
  const rec=mov.filter(t=>t.type==='income').reduce((s,t)=>s+t.amount,0);
  const desp=mov.filter(t=>t.type==='expense').reduce((s,t)=>s+t.amount,0);
  return (conta.saldo||0)+rec-desp;
}

// ── LIMITE UTILIZADO ──
function calcLimiteUsado(cartao){
  const txs=VELORA.db.getTransactions();
  const now=new Date();
  const m=String(now.getMonth()+1).padStart(2,'0');
  const y=now.getFullYear();
  return txs.filter(t=>t.type==='expense'&&t.account===cartao.nome&&t.date&&t.date.startsWith(y+'-'+m))
    .reduce((s,t)=>s+t.amount,0);
}
</script>
""")

H.append("""<script>
// ── SUMMARY ──
function renderSummary(){
  const contas=getContas();
  const cartoes=getCartoes();
  const saldoTotal=contas.filter(c=>c.situacao!=='inativa').reduce((s,c)=>s+calcSaldo(c),0);
  const limiteTotal=cartoes.reduce((s,c)=>s+(c.limite||0),0);
  const usado=cartoes.reduce((s,c)=>s+calcLimiteUsado(c),0);
  const faturas=getFaturas().filter(f=>f.situacao==='pendente'||f.situacao==='aberta');
  const fatTotal=faturas.reduce((s,f)=>s+(f.valor||0),0);
  document.getElementById('sumSaldo').textContent=fmt(saldoTotal);
  document.getElementById('sumLimite').textContent=fmt(Math.max(0,limiteTotal-usado));
  document.getElementById('sumFatura').textContent=fmt(fatTotal);
  document.getElementById('sumContas').textContent=contas.filter(c=>c.situacao!=='inativa').length;
}

// ── BANK GRID ──
function renderBankGrid(){
  const contas=getContas();
  const grid=document.getElementById('bankGrid');
  let html=contas.map(c=>{
    const saldo=calcSaldo(c);
    const badge=c.situacao==='inativa'?'badge-inativa':'badge-ativa';
    const badgeTxt=c.situacao==='inativa'?'Inativa':'Ativa';
    return `<div class="bank-card" style="--ac:${c.cor}">
      <div class="bank-card-top">
        <div class="bank-card-icon" style="background:${c.cor}18;color:${c.cor}"><i class="fa ${TIPO_ICONS[c.tipo]||'fa-wallet'}"></i></div>
        <span class="bank-card-badge ${badge}">${badgeTxt}</span>
      </div>
      <div class="bank-card-name">${c.nome}</div>
      <div class="bank-card-inst">${c.inst||'—'}</div>
      <div class="bank-card-type">${TIPO_LABELS[c.tipo]||c.tipo}</div>
      <div class="bank-card-balance ${saldo>=0?'pos':'neg'}">${fmt(saldo)}</div>
      <div class="bank-card-actions">
        <button class="btn-detail" onclick="verDetalheConta(${c.id})"><i class="fa fa-eye"></i> Detalhes</button>
        <button onclick="editConta(${c.id})"><i class="fa fa-pen"></i> Editar</button>
        <button onclick="delConta(${c.id})"><i class="fa fa-trash"></i> Excluir</button>
      </div>
    </div>`;
  }).join('');
  html+=`<button class="bank-add-card" onclick="document.getElementById('btnAddConta').click()"><i class="fa fa-plus"></i><span>Adicionar conta</span></button>`;
  grid.innerHTML=html;
}

// ── CC CAROUSEL ──
function renderCarousel(){
  const cartoes=getCartoes();
  const el=document.getElementById('ccCarousel');
  if(!cartoes.length){
    el.innerHTML='<div style="color:#444;font-size:13px;padding:24px;text-align:center;width:100%"><i class="fa fa-credit-card" style="font-size:28px;display:block;margin-bottom:8px;opacity:.3"></i>Nenhum cartao cadastrado.</div>';
    return;
  }
  el.innerHTML=cartoes.map(c=>{
    const usado=calcLimiteUsado(c);
    const limite=c.limite||0;
    const pct=limite>0?Math.min(100,Math.round((usado/limite)*100)):0;
    const theme=THEMES[c.cor]||'th-dark';
    const brandIco=BRAND_ICONS[c.brand]||'fa fa-credit-card';
    const sel=selCardId===c.id?' sel':'';
    return `<div class="cc-visual ${theme}${sel}" onclick="selectCard(${c.id})">
      <div class="cc-shine"></div><div class="cc-circles"></div>
      <div class="cc-top"><span class="cc-bank-name">${c.nome}</span><i class="${brandIco} cc-brand-ico"></i></div>
      <div class="cc-chip"></div>
      <div class="cc-digits">**** **** **** ${c.digits||'0000'}</div>
      <div class="cc-bottom">
        <div><div class="cc-lbl">Limite</div><div class="cc-val">${fmt(limite)}</div></div>
        <div style="text-align:right"><div class="cc-lbl">Disponivel</div><div class="cc-val">${fmt(Math.max(0,limite-usado))}</div></div>
      </div>
      <div class="cc-limit-bar"><div class="cc-limit-fill" style="width:${pct}%;background:${c.cor}"></div></div>
    </div>`;
  }).join('');
}
</script>
""")

H.append("""<script>
// ── SELECT CARD ──
function selectCard(id){
  selCardId=id;
  renderCarousel();
  const c=getCartoes().find(x=>x.id===id);
  if(!c)return;
  document.getElementById('detailCardName').textContent=c.nome+' ****'+c.digits;
  renderDetailTab();
}

function renderDetailTab(){
  const c=getCartoes().find(x=>x.id===selCardId);
  const body=document.getElementById('detailBody');
  if(!c){body.innerHTML='<div class="detail-empty"><i class="fa fa-credit-card"></i>Selecione um cartao</div>';return;}
  if(activeTab==='resumo') renderTabResumo(c,body);
  else if(activeTab==='faturas') renderTabFaturas(c,body);
  else renderTabTransacoes(c,body);
}

function renderTabResumo(c,body){
  const usado=calcLimiteUsado(c);
  const limite=c.limite||0;
  const pct=limite>0?Math.min(100,Math.round((usado/limite)*100)):0;
  const avail=Math.max(0,limite-usado);
  const r=44, circ=2*Math.PI*r;
  const dash=circ*(pct/100);
  const color=pct<60?'#4ade80':pct<85?'#f59e0b':'#ff354f';
  body.innerHTML=`
    <div class="donut-wrap-sm">
      <div class="donut-sm">
        <svg width="100" height="100" viewBox="0 0 100 100">
          <circle cx="50" cy="50" r="${r}" fill="none" stroke="#222" stroke-width="10"/>
          <circle cx="50" cy="50" r="${r}" fill="none" stroke="${color}" stroke-width="10"
            stroke-dasharray="${dash} ${circ}" stroke-linecap="round" style="transition:stroke-dasharray .5s"/>
        </svg>
        <div class="donut-sm-center"><div class="donut-sm-pct" style="color:${color}">${pct}%</div><div class="donut-sm-lbl">usado</div></div>
      </div>
      <div style="flex:1">
        <div class="detail-stat-row"><span class="detail-stat-lbl">Limite total</span><span class="detail-stat-val">${fmt(limite)}</span></div>
        <div class="detail-stat-row"><span class="detail-stat-lbl">Utilizado</span><span class="detail-stat-val" style="color:#ff354f">${fmt(usado)}</span></div>
        <div class="detail-stat-row"><span class="detail-stat-lbl">Disponivel</span><span class="detail-stat-val" style="color:#4ade80">${fmt(avail)}</span></div>
      </div>
    </div>
    <div class="detail-stat-row"><span class="detail-stat-lbl">Dia de fechamento</span><span class="detail-stat-val">Dia ${c.fecha||'—'}</span></div>
    <div class="detail-stat-row"><span class="detail-stat-lbl">Dia de vencimento</span><span class="detail-stat-val">Dia ${c.vence||'—'}</span></div>
    <div class="detail-stat-row"><span class="detail-stat-lbl">Bandeira</span><span class="detail-stat-val">${(c.brand||'').charAt(0).toUpperCase()+(c.brand||'').slice(1)}</span></div>
    <div style="margin-top:12px;display:flex;gap:8px">
      <button class="btn-save" style="font-size:12px;padding:8px 14px" onclick="editCartao(${c.id})"><i class="fa fa-pen"></i> Editar</button>
      <button class="btn-cancel" style="font-size:12px;padding:8px 14px;color:#ff354f;border-color:rgba(255,53,79,.3)" onclick="delCartao(${c.id})"><i class="fa fa-trash"></i> Excluir</button>
    </div>`;
}

function renderTabFaturas(c,body){
  const fats=getFaturas().filter(f=>f.cartaoId===c.id).sort((a,b)=>b.comp>a.comp?1:-1).slice(0,8);
  if(!fats.length){body.innerHTML='<div class="detail-empty"><i class="fa fa-file-invoice"></i>Nenhuma fatura encontrada.</div>';return;}
  body.innerHTML=fats.map(f=>{
    const cls=f.situacao==='paga'?'fb-paga':f.situacao==='vencida'?'fb-vencida':'fb-pendente';
    const lbl=f.situacao==='paga'?'Paga':f.situacao==='vencida'?'Vencida':'Pendente';
    return `<div class="fatura-row">
      <span class="fatura-comp">${f.comp||'—'}</span>
      <span class="fatura-venc">${f.venc||'—'}</span>
      <span class="fatura-val">${fmt(f.valor)}</span>
      <span class="fatura-badge ${cls}">${lbl}</span>
    </div>`;
  }).join('');
}

function renderTabTransacoes(c,body){
  const txs=VELORA.db.getTransactions().filter(t=>t.account===c.nome).sort((a,b)=>b.date>a.date?1:-1).slice(0,10);
  const cats=VELORA.db.getCategories();
  if(!txs.length){body.innerHTML='<div class="detail-empty"><i class="fa fa-receipt"></i>Nenhuma transacao neste cartao.</div>';return;}
  body.innerHTML=txs.map(t=>{
    const cat=cats.find(x=>x.name===t.category);
    const color=cat?cat.color:'#555';
    const icon=cat?cat.icon:'fa-circle';
    return `<div class="tx-row">
      <div class="tx-icon" style="background:${color}18;color:${color}"><i class="fa ${icon}"></i></div>
      <div class="tx-info"><div class="tx-desc">${t.description}</div><div class="tx-cat">${t.category||'—'}</div></div>
      <span class="tx-date">${fmtDate(t.date)}</span>
      <span class="tx-val ${t.type==='income'?'pos':'neg'}">${t.type==='income'?'+':'-'}${fmt(t.amount)}</span>
    </div>`;
  }).join('');
}

// ── PROXIMAS FATURAS ──
function renderFatList(){
  const fats=getFaturas().filter(f=>f.situacao!=='paga').sort((a,b)=>a.venc>b.venc?1:-1).slice(0,5);
  const el=document.getElementById('fatList');
  if(!fats.length){el.innerHTML='<div style="color:#444;font-size:13px;padding:16px;text-align:center">Nenhuma fatura pendente.</div>';return;}
  const cartoes=getCartoes();
  el.innerHTML=fats.map(f=>{
    const c=cartoes.find(x=>x.id===f.cartaoId);
    const cls=f.situacao==='vencida'?'fat-vencida':'fat-pendente';
    const badge=f.situacao==='vencida'?'fb-vencida':'fb-pendente';
    const lbl=f.situacao==='vencida'?'Vencida':'Pendente';
    return `<div class="fat-item ${cls}">
      <span class="fat-card-name">${c?c.nome:'—'}</span>
      <span class="fat-comp">${f.comp||'—'}</span>
      <span class="fat-venc">${f.venc||'—'}</span>
      <span class="fat-val">${fmt(f.valor)}</span>
      <span class="fatura-badge ${badge}">${lbl}</span>
    </div>`;
  }).join('');
}

// ── TABS ──
document.querySelectorAll('.detail-tab').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('.detail-tab').forEach(b=>b.classList.remove('active'));
    btn.classList.add('active');
    activeTab=btn.dataset.tab;
    renderDetailTab();
  });
});

function render(){
  renderSummary();
  renderBankGrid();
  renderCarousel();
  renderDetailTab();
  renderFatList();
}
</script>
""")

H.append("""<script>
// ── COLOR PICKERS ──
function buildColorRow(rowId,selColor,onPick){
  document.getElementById(rowId).innerHTML=COLORS.map(c=>
    `<div class="color-dot${c===selColor?' sel':''}" style="background:${c}" onclick="${onPick}('${c}')"></div>`
  ).join('');
}
function pickContaColor(c){selContaColor=c;buildColorRow('contaColorRow',c,'pickContaColor');}
function pickCartaoColor(c){selCartaoColor=c;buildColorRow('cartaoColorRow',c,'pickCartaoColor');}

// ── MODAL CONTA ──
function openModalConta(acc){
  editContaId=acc?acc.id:null;
  document.getElementById('modalContaTitulo').textContent=acc?'Editar Conta':'Nova Conta';
  document.getElementById('cNome').value=acc?acc.nome:'';
  document.getElementById('cInst').value=acc?acc.inst||'':'';
  document.getElementById('cTipo').value=acc?acc.tipo:'corrente';
  document.getElementById('cSaldo').value=acc?acc.saldo||'':'';
  document.getElementById('cData').value=acc?acc.dataSaldo||'':'';
  document.getElementById('cSituacao').value=acc?acc.situacao||'ativa':'ativa';
  selContaColor=acc?acc.cor:COLORS[0];
  buildColorRow('contaColorRow',selContaColor,'pickContaColor');
  document.getElementById('modalConta').classList.add('open');
}
function closeModalConta(){document.getElementById('modalConta').classList.remove('open');editContaId=null;}
document.getElementById('btnAddConta').addEventListener('click',()=>openModalConta(null));
document.getElementById('modalContaClose').addEventListener('click',closeModalConta);
document.getElementById('modalContaCancel').addEventListener('click',closeModalConta);
document.getElementById('modalConta').addEventListener('click',e=>{if(e.target===e.currentTarget)closeModalConta();});
document.getElementById('modalContaSave').addEventListener('click',()=>{
  const nome=document.getElementById('cNome').value.trim();
  if(!nome){VELORA.showToast('error','Informe o nome da conta.');return;}
  const acc={
    id:editContaId||genId(),nome,
    inst:document.getElementById('cInst').value.trim(),
    tipo:document.getElementById('cTipo').value,
    saldo:parseFloat(document.getElementById('cSaldo').value)||0,
    dataSaldo:document.getElementById('cData').value,
    situacao:document.getElementById('cSituacao').value,
    cor:selContaColor
  };
  let arr=getContas();
  if(editContaId){arr=arr.map(a=>a.id===editContaId?acc:a);VELORA.showToast('success','Conta atualizada!');}
  else{arr.push(acc);VELORA.showToast('success','Conta criada!');}
  saveContas(arr);closeModalConta();render();
});
function editConta(id){const a=getContas().find(x=>x.id===id);if(a)openModalConta(a);}
function delConta(id){
  if(!confirm('Excluir esta conta?'))return;
  saveContas(getContas().filter(a=>a.id!==id));
  VELORA.showToast('success','Conta excluida.');render();
}

// ── MODAL CARTAO ──
function populateContaSelect(){
  const sel=document.getElementById('ccConta');
  sel.innerHTML='<option value="">Nenhuma</option>';
  getContas().forEach(c=>sel.innerHTML+=`<option value="${c.id}">${c.nome}</option>`);
}
function openModalCartao(acc){
  editCartaoId=acc?acc.id:null;
  document.getElementById('modalCartaoTitulo').textContent=acc?'Editar Cartao':'Novo Cartao';
  document.getElementById('ccNome').value=acc?acc.nome:'';
  document.getElementById('ccDigits').value=acc?acc.digits||'':'';
  document.getElementById('ccLimite').value=acc?acc.limite||'':'';
  document.getElementById('ccFecha').value=acc?acc.fecha||'':'';
  document.getElementById('ccVence').value=acc?acc.vence||'':'';
  selBrand=acc?acc.brand||'visa':'visa';
  selCartaoColor=acc?acc.cor:COLORS[0];
  document.querySelectorAll('#brandRow .brand-btn').forEach(b=>b.classList.toggle('sel',b.dataset.brand===selBrand));
  buildColorRow('cartaoColorRow',selCartaoColor,'pickCartaoColor');
  populateContaSelect();
  if(acc&&acc.contaId)document.getElementById('ccConta').value=acc.contaId;
  document.getElementById('modalCartao').classList.add('open');
}
function closeModalCartao(){document.getElementById('modalCartao').classList.remove('open');editCartaoId=null;}
document.getElementById('btnAddCard').addEventListener('click',()=>openModalCartao(null));
document.getElementById('modalCartaoClose').addEventListener('click',closeModalCartao);
document.getElementById('modalCartaoCancel').addEventListener('click',closeModalCartao);
document.getElementById('modalCartao').addEventListener('click',e=>{if(e.target===e.currentTarget)closeModalCartao();});
document.querySelectorAll('#brandRow .brand-btn').forEach(btn=>{
  btn.addEventListener('click',()=>{
    document.querySelectorAll('#brandRow .brand-btn').forEach(b=>b.classList.remove('sel'));
    btn.classList.add('sel');selBrand=btn.dataset.brand;
  });
});
document.getElementById('modalCartaoSave').addEventListener('click',()=>{
  const nome=document.getElementById('ccNome').value.trim();
  if(!nome){VELORA.showToast('error','Informe o nome do cartao.');return;}
  const digits=document.getElementById('ccDigits').value.replace(/\\D/g,'').slice(0,4);
  if(digits&&digits.length!==4){VELORA.showToast('error','Informe os 4 ultimos digitos.');return;}
  const acc={
    id:editCartaoId||genId(),nome,digits,brand:selBrand,
    limite:parseFloat(document.getElementById('ccLimite').value)||0,
    fecha:parseInt(document.getElementById('ccFecha').value)||1,
    vence:parseInt(document.getElementById('ccVence').value)||10,
    contaId:document.getElementById('ccConta').value||null,
    cor:selCartaoColor
  };
  let arr=getCartoes();
  if(editCartaoId){arr=arr.map(a=>a.id===editCartaoId?acc:a);VELORA.showToast('success','Cartao atualizado!');}
  else{arr.push(acc);VELORA.showToast('success','Cartao adicionado!');}
  saveCartoes(arr);closeModalCartao();selCardId=acc.id;render();
});
function editCartao(id){const a=getCartoes().find(x=>x.id===id);if(a)openModalCartao(a);}
function delCartao(id){
  if(!confirm('Excluir este cartao?'))return;
  saveCartoes(getCartoes().filter(a=>a.id!==id));
  if(selCardId===id)selCardId=null;
  VELORA.showToast('success','Cartao excluido.');render();
}

// ── DETALHE CONTA ──
function verDetalheConta(id){
  const c=getContas().find(x=>x.id===id);if(!c)return;
  const saldo=calcSaldo(c);
  const txs=VELORA.db.getTransactions().filter(t=>t.account===c.nome).sort((a,b)=>b.date>a.date?1:-1).slice(0,8);
  const cats=VELORA.db.getCategories();
  document.getElementById('detalheContaTitulo').textContent=c.nome;
  document.getElementById('detalheContaBody').innerHTML=`
    <div class="dm-stat"><div class="dm-stat-lbl">Saldo atual</div><div class="dm-stat-val" style="color:${saldo>=0?'#4ade80':'#ff354f'}">${fmt(saldo)}</div></div>
    <div class="dm-stat"><div class="dm-stat-lbl">Instituicao</div><div class="dm-stat-val">${c.inst||'—'}</div></div>
    <div class="dm-stat"><div class="dm-stat-lbl">Tipo</div><div class="dm-stat-val">${TIPO_LABELS[c.tipo]||c.tipo}</div></div>
    <div style="margin:14px 0 8px;font-size:11px;color:#555;text-transform:uppercase;letter-spacing:.5px">Ultimas movimentacoes</div>
    ${txs.length?txs.map(t=>{
      const cat=cats.find(x=>x.name===t.category);
      const color=cat?cat.color:'#555';
      const icon=cat?cat.icon:'fa-circle';
      return `<div class="tx-row">
        <div class="tx-icon" style="background:${color}18;color:${color}"><i class="fa ${icon}"></i></div>
        <div class="tx-info"><div class="tx-desc">${t.description}</div><div class="tx-cat">${t.category||'—'}</div></div>
        <span class="tx-date">${fmtDate(t.date)}</span>
        <span class="tx-val ${t.type==='income'?'pos':'neg'}">${t.type==='income'?'+':'-'}${fmt(t.amount)}</span>
      </div>`;
    }).join(''):'<div style="color:#444;font-size:12px;padding:12px 0">Nenhuma movimentacao.</div>'}`;
  document.getElementById('modalDetalheConta').classList.add('open');
}
document.getElementById('modalDetalheContaClose').addEventListener('click',()=>document.getElementById('modalDetalheConta').classList.remove('open'));
document.getElementById('modalDetalheConta').addEventListener('click',e=>{if(e.target===e.currentTarget)document.getElementById('modalDetalheConta').classList.remove('open');});

render();
window.addEventListener('velora:update',render);
window.addEventListener('storage',render);
</script>
</body>
</html>
""")

with open(P,'w',encoding='utf-8') as f:
    f.write(''.join(H))
print('contas.html written:', len(''.join(H)), 'chars')
