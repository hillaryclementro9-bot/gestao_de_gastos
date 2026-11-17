from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

metas_bp = Blueprint('metas', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@metas_bp.route('/api/metas', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    return jsonify(ok=True, data=query('SELECT * FROM meta WHERE id_usuario=? ORDER BY data_limite', (uid,)))

@metas_bp.route('/api/metas', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    if not d.get('nome') or not d.get('valor_alvo'):
        return jsonify(ok=False, msg='Nome e valor alvo obrigatórios.')
    lid = execute("""
        INSERT INTO meta (id_usuario,nome,valor_alvo,valor_acumulado,data_limite)
        VALUES (?,?,?,?,?)
    """, (uid, d['nome'], d['valor_alvo'], d.get('valor_acumulado',0), d.get('data_limite')))
    return jsonify(ok=True, id=lid)

@metas_bp.route('/api/metas/<int:mid>', methods=['PUT'])
@login_required
def atualizar(mid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE meta SET nome=?,valor_alvo=?,valor_acumulado=?,data_limite=?
        WHERE id_meta=? AND id_usuario=?
    """, (d['nome'], d['valor_alvo'], d.get('valor_acumulado',0), d.get('data_limite'), mid, uid))
    return jsonify(ok=True)

@metas_bp.route('/api/metas/<int:mid>/depositar', methods=['POST'])
@login_required
def depositar(mid):
    uid = session['user_id']
    d = request.get_json()
    valor = float(d.get('valor', 0))
    if valor <= 0:
        return jsonify(ok=False, msg='Valor inválido.')
    execute('UPDATE meta SET valor_acumulado = valor_acumulado + ? WHERE id_meta=? AND id_usuario=?',
            (valor, mid, uid))
    return jsonify(ok=True)

@metas_bp.route('/api/metas/<int:mid>', methods=['DELETE'])
@login_required
def deletar(mid):
    uid = session['user_id']
    execute('DELETE FROM meta WHERE id_meta=? AND id_usuario=?', (mid, uid))
    return jsonify(ok=True)
