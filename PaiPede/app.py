import os
import sys

# garante que imports relativos funcionem ao rodar app.py diretamente
sys.path.insert(0, os.path.dirname(__file__))

from flask import Flask, render_template, session, redirect, url_for
from datetime import date
from database.criar_tabelas import criar_tabelas
from backend.autenticacao import auth_bp
from backend.transacoes   import trans_bp
from backend.contas       import contas_bp
from backend.cartoes      import cartoes_bp
from backend.faturas      import faturas_bp
from backend.metas        import metas_bp
from backend.orcamentos   import orc_bp
from backend.categorias   import cat_bp
from backend.usuarios     import usuarios_bp

app = Flask(
    __name__,
    template_folder='frontend/templates',
    static_folder='frontend/static'
)
app.secret_key = os.environ.get('SECRET_KEY', 'paipede-secret-2024-xK9#mL')

# ── Blueprints ────────────────────────────────────────────────
for bp in [auth_bp, trans_bp, contas_bp, cartoes_bp,
           faturas_bp, metas_bp, orc_bp, cat_bp, usuarios_bp]:
    app.register_blueprint(bp)

# ── Helper ────────────────────────────────────────────────────
def logado():
    return 'user_id' in session

# ── Páginas ───────────────────────────────────────────────────
@app.route('/')
def index():
    if logado():
        return redirect(url_for('visao_geral'))
    return render_template('index.html')

@app.route('/cadastro')
def cadastro():
    if logado():
        return redirect(url_for('visao_geral'))
    return render_template('cadastro.html')

@app.route('/visao-geral')
def visao_geral():
    if not logado():
        return redirect(url_for('index'))
    return render_template('visao_geral.html', nome=session.get('user_nome',''), now=date.today().strftime('%Y-%m'))

@app.route('/transacoes')
def transacoes():
    if not logado():
        return redirect(url_for('index'))
    return render_template('transacoes.html', nome=session.get('user_nome',''))

@app.route('/cartoes-contas')
def cartoes_contas():
    if not logado():
        return redirect(url_for('index'))
    return render_template('cartoes_contas.html', nome=session.get('user_nome',''))

@app.route('/faturas')
def faturas():
    if not logado():
        return redirect(url_for('index'))
    return render_template('faturas.html', nome=session.get('user_nome',''))

@app.route('/planejamento')
def planejamento():
    if not logado():
        return redirect(url_for('index'))
    return render_template('planejamento.html', nome=session.get('user_nome',''))

@app.route('/configuracoes')
def configuracoes():
    if not logado():
        return redirect(url_for('index'))
    return render_template('configuracoes.html', nome=session.get('user_nome',''))

# ── Init ──────────────────────────────────────────────────────
if __name__ == '__main__':
    criar_tabelas()
    print('\n  PAI PEDE rodando em http://localhost:5000\n')
    app.run(debug=True, port=5000)
