from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

orc_bp = Blueprint('orcamentos', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@orc_bp.route('/api/orcamentos', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    mes = request.args.get('mes', '')
    rows = query("""
        SELECT o.*, c.nome as categoria_nome,
               COALESCE((
                   SELECT SUM(t.valor) FROM transacao t
                   WHERE t.id_usuario=o.id_usuario
                     AND t.id_categoria=o.id_categoria
                     AND strftime('%Y-%m',t.data_transacao)=o.mes_referencia
                     AND t.tipo='despesa' AND t.situacao!='cancelado'
               ),0) as gasto
        FROM orcamento o
        LEFT JOIN categoria c ON c.id_categoria = o.id_categoria
        WHERE o.id_usuario=? AND (o.mes_referencia=? OR ?='')
    """, (uid, mes, mes))
    return jsonify(ok=True, data=rows)

@orc_bp.route('/api/orcamentos', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    if not d.get('mes_referencia') or not d.get('valor_limite'):
        return jsonify(ok=False, msg='Mês e limite obrigatórios.')
    lid = execute("""
        INSERT INTO orcamento (id_usuario,id_categoria,mes_referencia,valor_limite)
        VALUES (?,?,?,?)
    """, (uid, d.get('id_categoria'), d['mes_referencia'], d['valor_limite']))
    return jsonify(ok=True, id=lid)

@orc_bp.route('/api/orcamentos/<int:oid>', methods=['PUT'])
@login_required
def atualizar(oid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE orcamento SET id_categoria=?,mes_referencia=?,valor_limite=?
        WHERE id_orcamento=? AND id_usuario=?
    """, (d.get('id_categoria'), d['mes_referencia'], d['valor_limite'], oid, uid))
    return jsonify(ok=True)

@orc_bp.route('/api/orcamentos/<int:oid>', methods=['DELETE'])
@login_required
def deletar(oid):
    uid = session['user_id']
    execute('DELETE FROM orcamento WHERE id_orcamento=? AND id_usuario=?', (oid, uid))
    return jsonify(ok=True)
