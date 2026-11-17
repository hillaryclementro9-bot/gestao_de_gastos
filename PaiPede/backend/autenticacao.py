from flask import Blueprint, request, session, jsonify, redirect, url_for
from werkzeug.security import generate_password_hash, check_password_hash
from database.conexao import query, execute

auth_bp = Blueprint('auth', __name__)

@auth_bp.route('/api/cadastro', methods=['POST'])
def cadastro():
    d = request.get_json()
    nome  = (d.get('nome') or '').strip()
    email = (d.get('email') or '').strip().lower()
    senha = d.get('senha') or ''
    conf  = d.get('confirmar') or ''

    if not nome or not email or not senha:
        return jsonify(ok=False, msg='Preencha todos os campos.')
    if len(senha) < 6:
        return jsonify(ok=False, msg='Senha mínimo 6 caracteres.')
    if senha != conf:
        return jsonify(ok=False, msg='Senhas não conferem.')

    existe = query('SELECT id_usuario FROM usuario WHERE email = ?', (email,))
    if existe:
        return jsonify(ok=False, msg='E-mail já cadastrado.')

    h = generate_password_hash(senha)
    uid = execute('INSERT INTO usuario (nome, email, senha_hash) VALUES (?, ?, ?)', (nome, email, h))

    session['user_id']   = uid
    session['user_nome'] = nome
    session['user_email']= email
    return jsonify(ok=True, redirect='/visao-geral')

@auth_bp.route('/api/login', methods=['POST'])
def login():
    d = request.get_json()
    email = (d.get('email') or '').strip().lower()
    senha = d.get('senha') or ''

    if not email or not senha:
        return jsonify(ok=False, msg='Preencha e-mail e senha.')

    rows = query('SELECT * FROM usuario WHERE email = ?', (email,))
    if not rows or not check_password_hash(rows[0]['senha_hash'], senha):
        return jsonify(ok=False, msg='E-mail ou senha incorretos.')

    u = rows[0]
    session['user_id']   = u['id_usuario']
    session['user_nome'] = u['nome']
    session['user_email']= u['email']
    return jsonify(ok=True, redirect='/visao-geral')

@auth_bp.route('/api/logout', methods=['POST'])
def logout():
    session.clear()
    return jsonify(ok=True)

@auth_bp.route('/api/recuperar-senha', methods=['POST'])
def recuperar_senha():
    d = request.get_json()
    email = (d.get('email') or '').strip().lower()
    nova  = d.get('nova_senha') or ''
    if not email or not nova:
        return jsonify(ok=False, msg='Dados incompletos.')
    rows = query('SELECT id_usuario FROM usuario WHERE email = ?', (email,))
    if not rows:
        return jsonify(ok=False, msg='E-mail não encontrado.')
    h = generate_password_hash(nova)
    execute('UPDATE usuario SET senha_hash = ? WHERE email = ?', (h, email))
    return jsonify(ok=True)
