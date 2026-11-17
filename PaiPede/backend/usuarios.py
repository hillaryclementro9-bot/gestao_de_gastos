from flask import Blueprint, request, session, jsonify
from werkzeug.security import generate_password_hash, check_password_hash
from database.conexao import query, execute
from functools import wraps

usuarios_bp = Blueprint('usuarios', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@usuarios_bp.route('/api/perfil', methods=['GET'])
@login_required
def get_perfil():
    uid = session['user_id']
    rows = query('SELECT id_usuario,nome,email,tema,notificacoes_ativas FROM usuario WHERE id_usuario=?', (uid,))
    return jsonify(ok=True, data=rows[0] if rows else {})

@usuarios_bp.route('/api/perfil', methods=['PUT'])
@login_required
def salvar_perfil():
    uid = session['user_id']
    d = request.get_json()
    execute('UPDATE usuario SET nome=?,email=?,tema=?,notificacoes_ativas=? WHERE id_usuario=?',
            (d.get('nome'), d.get('email'), d.get('tema','escuro'),
             int(d.get('notificacoes_ativas', 1)), uid))
    session['user_nome']  = d.get('nome')
    session['user_email'] = d.get('email')
    return jsonify(ok=True)

@usuarios_bp.route('/api/alterar-senha', methods=['POST'])
@login_required
def alterar_senha():
    uid = session['user_id']
    d = request.get_json()
    atual = d.get('senha_atual','')
    nova  = d.get('nova_senha','')
    if len(nova) < 6:
        return jsonify(ok=False, msg='Nova senha mínimo 6 caracteres.')
    rows = query('SELECT senha_hash FROM usuario WHERE id_usuario=?', (uid,))
    if not rows or not check_password_hash(rows[0]['senha_hash'], atual):
        return jsonify(ok=False, msg='Senha atual incorreta.')
    execute('UPDATE usuario SET senha_hash=? WHERE id_usuario=?',
            (generate_password_hash(nova), uid))
    return jsonify(ok=True)

@usuarios_bp.route('/api/session')
def get_session():
    if 'user_id' in session:
        return jsonify(ok=True, nome=session.get('user_nome'), email=session.get('user_email'))
    return jsonify(ok=False)
