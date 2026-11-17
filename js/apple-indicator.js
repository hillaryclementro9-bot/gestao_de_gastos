/* =====================================================
   PAI PEDE — Apple Financial Indicator v2
   Card "Sua saúde financeira" + Tutorial modal
===================================================== */

const VeloraApple = (() => {

  // ── SVG BUILDER ────────────────────────────────────
  // 5 visual states: 0 bites → 4 bites (progressive right-side bites)
  const BITE_DEFS = [
    { cx: 86, cy: 48, r: 22 },
    { cx: 88, cy: 70, r: 20 },
    { cx: 78, cy: 88, r: 18 },
    { cx: 60, cy: 97, r: 16 },
  ];

  function buildSVG(biteCount, bodyColor, size = 90) {
    const uid = 'va' + Math.random().toString(36).slice(2, 7);
    const activeBites = BITE_DEFS.slice(0, biteCount);

    const appleShape =
      'M50,22 C50,22 44,14 34,16 C18,19 5,36 5,63 C5,92 23,110 50,110 C77,110 95,92 95,63 C95,36 82,19 66,16 C56,14 50,22 50,22 Z';

    const biteHoles = activeBites.map(b =>
      `M${b.cx - b.r},${b.cy} a${b.r},${b.r} 0 1,0 ${b.r*2},0 a${b.r},${b.r} 0 1,0 -${b.r*2},0`
    ).join(' ');

    const clipBody = `${appleShape} ${biteHoles}`;

    // Flesh: creamy inner + darker ring
    const fleshCircles = activeBites.map(b => `
      <circle cx="${b.cx}" cy="${b.cy}" r="${b.r+1}" fill="#c8a06a" opacity="0.6"/>
      <circle cx="${b.cx}" cy="${b.cy}" r="${b.r-2}" fill="#f0d090" opacity="0.85"/>
      <circle cx="${b.cx}" cy="${b.cy}" r="${b.r-5}" fill="#faebd0" opacity="0.7"/>`
    ).join('');

    const biteRings = activeBites.map(b =>
      `<circle cx="${b.cx}" cy="${b.cy}" r="${b.r}" fill="none" stroke="rgba(0,0,0,0.25)" stroke-width="1.5"/>`
    ).join('');

    return `<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 122" width="${size}" height="${Math.round(size*1.22)}" aria-hidden="true">
  <defs>
    <clipPath id="${uid}clip">
      <path fill-rule="evenodd" d="${clipBody}"/>
    </clipPath>
    <!-- Drop shadow filter -->
    <filter id="${uid}shadow" x="-20%" y="-10%" width="140%" height="140%">
      <feDropShadow dx="0" dy="6" stdDeviation="6" flood-color="rgba(0,0,0,0.55)"/>
    </filter>
    <!-- Base gradient: bright highlight top-left → deep dark bottom-right -->
    <radialGradient id="${uid}base" cx="32%" cy="28%" r="70%">
      <stop offset="0%"   stop-color="#ff8080"/>
      <stop offset="30%"  stop-color="${bodyColor}"/>
      <stop offset="75%"  stop-color="${bodyColor}" stop-opacity="0.85"/>
      <stop offset="100%" stop-color="#1a0202"/>
    </radialGradient>
    <!-- Secondary depth shading bottom -->
    <radialGradient id="${uid}depth" cx="50%" cy="95%" r="55%">
      <stop offset="0%"   stop-color="rgba(0,0,0,0.45)"/>
      <stop offset="100%" stop-color="rgba(0,0,0,0)"/>
    </radialGradient>
    <!-- Primary specular (large soft) -->
    <radialGradient id="${uid}spec1" cx="36%" cy="26%" r="28%">
      <stop offset="0%"   stop-color="rgba(255,255,255,0.60)"/>
      <stop offset="60%"  stop-color="rgba(255,255,255,0.10)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
    </radialGradient>
    <!-- Secondary specular (small sharp) -->
    <radialGradient id="${uid}spec2" cx="30%" cy="22%" r="12%">
      <stop offset="0%"   stop-color="rgba(255,255,255,0.90)"/>
      <stop offset="100%" stop-color="rgba(255,255,255,0)"/>
    </radialGradient>
    <!-- Rim light left edge -->
    <linearGradient id="${uid}rim" x1="0%" y1="0%" x2="100%" y2="0%">
      <stop offset="0%"   stop-color="rgba(255,160,160,0.35)"/>
      <stop offset="18%"  stop-color="rgba(255,160,160,0)"/>
      <stop offset="82%"  stop-color="rgba(0,0,0,0)"/>
      <stop offset="100%" stop-color="rgba(0,0,0,0.30)"/>
    </linearGradient>
    <!-- Leaf gradient -->
    <linearGradient id="${uid}leaf" x1="0%" y1="0%" x2="100%" y2="100%">
      <stop offset="0%"   stop-color="#4ade80"/>
      <stop offset="100%" stop-color="#15803d"/>
    </linearGradient>
  </defs>

  <!-- Flesh behind bites -->
  ${fleshCircles}

  <!-- Apple body with shadow -->
  <g filter="url(#${uid}shadow)">
    <g clip-path="url(#${uid}clip)">
      <path d="${appleShape}" fill="url(#${uid}base)"/>
      <path d="${appleShape}" fill="url(#${uid}depth)"/>
      <path d="${appleShape}" fill="url(#${uid}rim)"/>
      <path d="${appleShape}" fill="url(#${uid}spec1)"/>
      <path d="${appleShape}" fill="url(#${uid}spec2)"/>
    </g>
  </g>

  <!-- Bite edge rings -->
  ${biteRings}

  <!-- Stem -->
  <path d="M50,22 C49,12 56,4 63,5" stroke="#6b3a1f" stroke-width="3.5" stroke-linecap="round" fill="none"/>
  <path d="M50,22 C49,12 56,4 63,5" stroke="rgba(255,200,150,0.25)" stroke-width="1.5" stroke-linecap="round" fill="none"/>

  <!-- Leaf -->
  <path d="M54,14 C62,3 77,7 70,19 C66,12 55,16 54,14 Z" fill="url(#${uid}leaf)"/>
  <path d="M54,14 C62,8 70,12 67,19" stroke="#166534" stroke-width="1" fill="none" opacity="0.6"/>
  <!-- Leaf shine -->
  <path d="M57,11 C62,6 70,9 68,14" stroke="rgba(255,255,255,0.35)" stroke-width="1" fill="none" stroke-linecap="round"/>
</svg>`;
  }

  // ── STATE LOGIC ────────────────────────────────────
  function calcState(income, expense, budgetLimit) {
    // Use budget limit if available, otherwise income
    const reference = budgetLimit > 0 ? budgetLimit : income;
    const ratio     = reference > 0 ? expense / reference : (expense > 0 ? 1.6 : 0);
    const pct       = Math.round(ratio * 100);

    if (ratio <= 0.60) return { bites: 0, color: '#dc2626', statusColor: '#22c55e', label: 'Finanças equilibradas',    msg: 'Seus gastos estão dentro do planejamento e você ainda consegue economizar.', pct };
    if (ratio <= 0.75) return { bites: 1, color: '#c0392b', statusColor: '#f59e0b', label: 'Atenção aos gastos',       msg: 'Seus gastos estão aumentando e começando a comprometer sua capacidade de economizar.', pct };
    if (ratio <= 0.90) return { bites: 2, color: '#a93226', statusColor: '#f97316', label: 'Limite do orçamento',      msg: 'Você está próximo do limite. Revise seus gastos para não comprometer suas reservas.', pct };
    if (ratio <= 1.10) return { bites: 3, color: '#922b21', statusColor: '#ef4444', label: 'Orçamento comprometido',   msg: 'Seus gastos ultrapassaram o limite planejado e estão comprometendo suas finanças.', pct };
    return               { bites: 4, color: '#7b241c', statusColor: '#b91c1c', label: 'Situação crítica',         msg: 'Suas despesas superam suas receitas. É urgente revisar seus gastos.', pct };
  }

  // ── CARD RENDER ────────────────────────────────────
  function render(containerId, month, year) {
    const container = document.getElementById(containerId);
    if (!container) return;

    const txs = VELORA.db.getTransactions();
    const now = new Date();
    const m   = month  !== undefined ? month  : now.getMonth();
    const y   = year   !== undefined ? year   : now.getFullYear();

    const filtered = txs.filter(t => {
      const d = new Date(t.date + 'T00:00:00');
      return d.getMonth() === m && d.getFullYear() === y;
    });

    const income  = filtered.filter(t => t.type === 'income').reduce((s, t) => s + t.amount, 0);
    const expense = filtered.filter(t => t.type === 'expense').reduce((s, t) => s + t.amount, 0);
    const balance = income - expense;

    // Try to find a "Geral" budget for the period
    const budgets     = VELORA.db.getBudgets();
    const geralBudget = budgets.find(b => b.category === 'Geral');
    const budgetLimit = geralBudget ? geralBudget.limit : 0;

    const state    = calcState(income, expense, budgetLimit);
    const hasBudget = budgetLimit > 0;

    // Progress bar width capped at 100% visually
    const barPct   = Math.min(state.pct, 100);
    const barColor = state.statusColor;

    container.innerHTML = `
      <div class="apple-card-inner">
        <div class="apple-card-header">
          <h2>Sua saúde financeira</h2>
          <span class="apple-period-badge">${_monthName(m)} ${y}</span>
        </div>

        <div class="apple-card-body">
          <div class="apple-svg-col">
            <div class="apple-svg-wrap">
              ${buildSVG(state.bites, state.color, 110)}
            </div>
            <div class="apple-status-badge" style="color:${state.statusColor};border-color:${state.statusColor}22;background:${state.statusColor}11">
              ${state.label}
            </div>
          </div>

          <div class="apple-info-col">
            <div class="apple-msg">${state.msg}</div>

            <div class="apple-progress-wrap">
              <div class="apple-progress-labels">
                <span>Orçamento utilizado</span>
                <span style="color:${barColor};font-weight:700">${state.pct}%</span>
              </div>
              <div class="apple-progress-track">
                <div class="apple-progress-fill" style="width:${barPct}%;background:${barColor}"></div>
              </div>
              ${!hasBudget ? `<div class="apple-no-budget"><i class="fa fa-circle-info"></i> Sem orçamento cadastrado — usando receita como referência. <a href="planejamento.html">Configurar</a></div>` : ''}
            </div>

            <div class="apple-stats">
              <div class="apple-stat">
                <span class="apple-stat-label"><i class="fa fa-arrow-trend-up"></i> Receitas</span>
                <span class="apple-stat-value income">${VELORA.fmt(income)}</span>
              </div>
              <div class="apple-stat">
                <span class="apple-stat-label"><i class="fa fa-arrow-trend-down"></i> Despesas</span>
                <span class="apple-stat-value expense">${VELORA.fmt(expense)}</span>
              </div>
              <div class="apple-stat">
                <span class="apple-stat-label"><i class="fa fa-wallet"></i> Saldo</span>
                <span class="apple-stat-value ${balance < 0 ? 'expense' : 'income'}">${VELORA.fmt(balance)}</span>
              </div>
            </div>
          </div>
        </div>
      </div>`;
  }

  // ── TUTORIAL MODAL ─────────────────────────────────
  function showTutorial(onClose) {
    if (document.getElementById('apple-tutorial-modal')) return;

    const steps = [
      {
        bites: 0, color: '#dc2626', statusColor: '#22c55e',
        emoji: '✅', tag: 'Ótimo',
        title: 'Maçã inteira',
        subtitle: 'Finanças equilibradas',
        desc: 'Seus gastos estão abaixo de 60% da sua receita. Você está economizando e no caminho certo!',
        tip: 'Continue assim e sua reserva vai crescer.',
      },
      {
        bites: 2, color: '#a93226', statusColor: '#f97316',
        emoji: '⚠️', tag: 'Atenção',
        title: 'Maçã com mordidas',
        subtitle: 'Gastos aumentando',
        desc: 'Seus gastos estão entre 75% e 90% da receita. Sua capacidade de economizar está diminuindo.',
        tip: 'Revise assinaturas e gastos variáveis.',
      },
      {
        bites: 4, color: '#7b241c', statusColor: '#ef4444',
        emoji: '🚨', tag: 'Crítico',
        title: 'Maçã muito mordida',
        subtitle: 'Orçamento comprometido',
        desc: 'Seus gastos superam sua receita. Suas finanças estão no vermelho e precisam de atenção urgente.',
        tip: 'Corte gastos não essenciais imediatamente.',
      },
    ];

    const modal = document.createElement('div');
    modal.id = 'apple-tutorial-modal';
    modal.innerHTML = `
      <div class="atm-backdrop"></div>
      <div class="atm-box" role="dialog" aria-modal="true" aria-labelledby="atm-title">

        <div class="atm-header">
          <div class="atm-logo">PAI <span>PEDE</span></div>
          <div class="atm-badge">Novo recurso</div>
        </div>

        <div class="atm-hero">
          <div class="atm-hero-apple">${buildSVG(0, '#dc2626', 80)}</div>
          <div class="atm-hero-text">
            <h2 id="atm-title">Sua Maçã Financeira</h2>
            <p>No PAI PEDE, a maçã é o termômetro da sua saúde financeira. Ela muda de acordo com a relação entre o que você <strong>ganha</strong> e o que você <strong>gasta</strong> no mês.</p>
          </div>
        </div>

        <div class="atm-divider"><span>Como funciona</span></div>

        <div class="atm-steps">
          ${steps.map((s, i) => `
            <div class="atm-step">
              <div class="atm-step-num">${i + 1}</div>
              <div class="atm-step-apple" style="filter:drop-shadow(0 4px 12px rgba(0,0,0,.5))">
                ${buildSVG(s.bites, s.color, 64)}
              </div>
              <div class="atm-step-info">
                <div class="atm-step-head">
                  <span class="atm-step-title">${s.title}</span>
                  <span class="atm-step-tag" style="color:${s.statusColor};background:${s.statusColor}18;border-color:${s.statusColor}33">${s.emoji} ${s.tag}</span>
                </div>
                <div class="atm-step-sub">${s.subtitle}</div>
                <div class="atm-step-desc">${s.desc}</div>
                <div class="atm-step-tip"><i class="fa fa-lightbulb"></i> ${s.tip}</div>
              </div>
            </div>`).join('')}
        </div>

        <div class="atm-footer-note">
          <i class="fa fa-circle-info"></i>
          A maçã é atualizada automaticamente todo mês conforme você lança suas transações.
        </div>

        <button class="atm-btn" id="atm-confirm">
          <i class="fa fa-check"></i> Entendi, vamos começar!
        </button>
      </div>`;

    document.body.appendChild(modal);
    requestAnimationFrame(() => modal.classList.add('atm-visible'));

    document.getElementById('atm-confirm').addEventListener('click', () => {
      VELORA.markTutorialSeen('maca');
      modal.classList.remove('atm-visible');
      setTimeout(() => { modal.remove(); if (onClose) onClose(); }, 320);
    });
  }

  function maybeShowTutorial(onClose) {
    if (!VELORA.hasSeenTutorial('maca')) {
      // Small delay so dashboard renders first
      setTimeout(() => showTutorial(onClose), 600);
    }
  }

  // ── HELPERS ────────────────────────────────────────
  function _monthName(m) {
    return ['Janeiro','Fevereiro','Março','Abril','Maio','Junho','Julho','Agosto','Setembro','Outubro','Novembro','Dezembro'][m];
  }

  return { render, maybeShowTutorial, buildSVG, calcState };
})();
