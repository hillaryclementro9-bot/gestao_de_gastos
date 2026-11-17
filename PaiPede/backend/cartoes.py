from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

cartoes_bp = Blueprint('cartoes', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@cartoes_bp.route('/api/cartoes', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    rows = query("""
        SELECT ca.*, co.nome as conta_nome,
               COALESCE((SELECT SUM(t.valor) FROM transacao t
                WHERE t.id_cartao=ca.id_cartao AND t.situacao='pago'
                  AND strftime('%Y-%m',t.data_transacao)=strftime('%Y-%m',date('now'))),0) as fatura_atual
        FROM cartao ca
        LEFT JOIN conta co ON co.id_conta = ca.id_conta
        WHERE ca.id_usuario=? ORDER BY ca.id_cartao
    """, (uid,))
    return jsonify(ok=True, data=rows)

@cartoes_bp.route('/api/cartoes', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    if not d.get('nome'):
        return jsonify(ok=False, msg='Nome obrigatório.')
    lid = execute("""
        INSERT INTO cartao (id_usuario,nome,ultimos_4_digitos,limite_total,dia_fechamento,dia_vencimento,id_conta)
        VALUES (?,?,?,?,?,?,?)
    """, (uid, d['nome'], d.get('ultimos_4_digitos',''), d.get('limite_total',0),
          d.get('dia_fechamento',1), d.get('dia_vencimento',10), d.get('id_conta')))
    return jsonify(ok=True, id=lid)

@cartoes_bp.route('/api/cartoes/<int:cid>', methods=['PUT'])
@login_required
def atualizar(cid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE cartao SET nome=?,ultimos_4_digitos=?,limite_total=?,dia_fechamento=?,dia_vencimento=?,id_conta=?
        WHERE id_cartao=? AND id_usuario=?
    """, (d['nome'], d.get('ultimos_4_digitos',''), d.get('limite_total',0),
          d.get('dia_fechamento',1), d.get('dia_vencimento',10),
          d.get('id_conta'), cid, uid))
    return jsonify(ok=True)

@cartoes_bp.route('/api/cartoes/<int:cid>', methods=['DELETE'])
@login_required
def deletar(cid):
    uid = session['user_id']
    execute('DELETE FROM cartao WHERE id_cartao=? AND id_usuario=?', (cid, uid))
    return jsonify(ok=True)
