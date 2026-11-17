from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

contas_bp = Blueprint('contas', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@contas_bp.route('/api/contas', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    return jsonify(ok=True, data=query('SELECT * FROM conta WHERE id_usuario=? ORDER BY id_conta', (uid,)))

@contas_bp.route('/api/contas', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    if not d.get('nome'):
        return jsonify(ok=False, msg='Nome obrigatório.')
    lid = execute("""
        INSERT INTO conta (id_usuario,nome,instituicao,tipo_conta,saldo_inicial,data_saldo_inicial)
        VALUES (?,?,?,?,?,?)
    """, (uid, d['nome'], d.get('instituicao',''), d.get('tipo_conta','corrente'),
          d.get('saldo_inicial',0), d.get('data_saldo_inicial')))
    return jsonify(ok=True, id=lid)

@contas_bp.route('/api/contas/<int:cid>', methods=['PUT'])
@login_required
def atualizar(cid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE conta SET nome=?,instituicao=?,tipo_conta=?,saldo_inicial=?,data_saldo_inicial=?
        WHERE id_conta=? AND id_usuario=?
    """, (d['nome'], d.get('instituicao',''), d.get('tipo_conta','corrente'),
          d.get('saldo_inicial',0), d.get('data_saldo_inicial'), cid, uid))
    return jsonify(ok=True)

@contas_bp.route('/api/contas/<int:cid>', methods=['DELETE'])
@login_required
def deletar(cid):
    uid = session['user_id']
    execute('DELETE FROM conta WHERE id_conta=? AND id_usuario=?', (cid, uid))
    return jsonify(ok=True)
