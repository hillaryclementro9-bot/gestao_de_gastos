/* =====================================================
   PAI PEDE — API Client
   Substitui localStorage pela API PHP
===================================================== */

const API = (() => {
  const BASE = '../php/api.php';

  async function call(action, body = null) {
    const opts = { method: body !== null ? 'POST' : 'GET', headers: {} };
    if (body !== null) {
      opts.headers['Content-Type'] = 'application/json';
      opts.body = JSON.stringify(body);
    }
    const res  = await fetch(`${BASE}?action=${action}`, opts);
    const data = await res.json();
    if (!data.ok && data.msg === 'Não autenticado.') {
      window.location.replace('/pages/login.php');
    }
    return data;
  }

  return {
    // Auth
    login:   (email, password) => call('login',   { email, password }),
    cadastro:(name, email, password) => call('cadastro', { name, email, password }),
    admin:   ()                => call('admin',   {}),
    logout:  ()                => call('logout',  {}),
    session: ()                => call('session'),

    // Transactions
    getTransactions:    ()   => call('get_transactions'),
    saveTransaction:    (d)  => call('save_transaction', d),
    deleteTransaction:  (id) => call('delete_transaction', { id }),

    // Bills
    getBills:    ()   => call('get_bills'),
    saveBill:    (d)  => call('save_bill', d),
    payBill:     (d)  => call('pay_bill', d),
    deleteBill:  (id) => call('delete_bill', { id }),

    // Accounts
    getAccounts:    ()   => call('get_accounts'),
    saveAccount:    (d)  => call('save_account', d),
    deleteAccount:  (id) => call('delete_account', { id }),

    // Budgets
    getBudgets:    ()   => call('get_budgets'),
    saveBudget:    (d)  => call('save_budget', d),
    deleteBudget:  (id) => call('delete_budget', { id }),

    // Goals
    getGoals:    ()   => call('get_goals'),
    saveGoal:    (d)  => call('save_goal', d),
    deleteGoal:  (id) => call('delete_goal', { id }),

    // Categories
    getCategories:    ()   => call('get_categories'),
    saveCategory:     (d)  => call('save_category', d),
    deleteCategory:   (id) => call('delete_category', { id }),
  };
})();
