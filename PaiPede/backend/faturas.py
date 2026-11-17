from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps
from datetime import date

faturas_bp = Blueprint('faturas', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@faturas_bp.route('/api/faturas', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    rows = query("""
        SELECT f.*, ca.nome as cartao_nome
        FROM fatura f
        JOIN cartao ca ON ca.id_cartao = f.id_cartao
        WHERE ca.id_usuario=?
        ORDER BY f.competencia DESC
    """, (uid,))
    return jsonify(ok=True, data=rows)

@faturas_bp.route('/api/faturas', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    chk = query('SELECT id_cartao FROM cartao WHERE id_cartao=? AND id_usuario=?',
                (d.get('id_cartao'), uid))
    if not chk:
        return jsonify(ok=False, msg='Cartão inválido.')
    lid = execute("""
        INSERT INTO fatura (id_cartao,competencia,data_fechamento,data_vencimento,data_pagamento,situacao)
        VALUES (?,?,?,?,?,?)
    """, (d['id_cartao'], d['competencia'], d.get('data_fechamento'),
          d.get('data_vencimento'), d.get('data_pagamento'), d.get('situacao','Pendente')))
    return jsonify(ok=True, id=lid)

@faturas_bp.route('/api/faturas/<int:fid>', methods=['PUT'])
@login_required
def atualizar(fid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE fatura SET competencia=?,data_fechamento=?,data_vencimento=?,data_pagamento=?,situacao=?
        WHERE id_fatura=?
          AND id_cartao IN (SELECT id_cartao FROM cartao WHERE id_usuario=?)
    """, (d['competencia'], d.get('data_fechamento'), d.get('data_vencimento'),
          d.get('data_pagamento'), d.get('situacao','Pendente'), fid, uid))
    return jsonify(ok=True)

@faturas_bp.route('/api/faturas/<int:fid>/pagar', methods=['POST'])
@login_required
def pagar(fid):
    uid = session['user_id']
    d = request.get_json() or {}
    dt = d.get('data_pagamento') or str(date.today())
    execute("""
        UPDATE fatura SET situacao='Pago', data_pagamento=?
        WHERE id_fatura=?
          AND id_cartao IN (SELECT id_cartao FROM cartao WHERE id_usuario=?)
    """, (dt, fid, uid))
    return jsonify(ok=True)

@faturas_bp.route('/api/faturas/<int:fid>', methods=['DELETE'])
@login_required
def deletar(fid):
    uid = session['user_id']
    execute("""
        DELETE FROM fatura WHERE id_fatura=?
          AND id_cartao IN (SELECT id_cartao FROM cartao WHERE id_usuario=?)
    """, (fid, uid))
    return jsonify(ok=True)
