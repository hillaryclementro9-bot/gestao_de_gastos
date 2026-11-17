from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

cat_bp = Blueprint('categorias', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@cat_bp.route('/api/categorias', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    rows = query("""
        SELECT * FROM categoria
        WHERE id_usuario IS NULL OR id_usuario=?
        ORDER BY tipo, nome
    """, (uid,))
    return jsonify(ok=True, data=rows)

@cat_bp.route('/api/categorias', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    if not d.get('nome') or not d.get('tipo'):
        return jsonify(ok=False, msg='Nome e tipo obrigatórios.')
    lid = execute('INSERT INTO categoria (id_usuario,nome,tipo) VALUES (?,?,?)',
                  (uid, d['nome'], d['tipo']))
    return jsonify(ok=True, id=lid)

@cat_bp.route('/api/categorias/<int:cid>', methods=['PUT'])
@login_required
def atualizar(cid):
    uid = session['user_id']
    d = request.get_json()
    chk = query('SELECT id_categoria FROM categoria WHERE id_categoria=? AND id_usuario=?', (cid, uid))
    if not chk:
        return jsonify(ok=False, msg='Categoria não encontrada ou padrão.')
    execute('UPDATE categoria SET nome=?,tipo=? WHERE id_categoria=? AND id_usuario=?',
            (d['nome'], d['tipo'], cid, uid))
    return jsonify(ok=True)

@cat_bp.route('/api/categorias/<int:cid>', methods=['DELETE'])
@login_required
def deletar(cid):
    uid = session['user_id']
    chk = query('SELECT id_categoria FROM categoria WHERE id_categoria=? AND id_usuario=?', (cid, uid))
    if not chk:
        return jsonify(ok=False, msg='Categoria não encontrada ou padrão.')
    em_uso = query('SELECT 1 FROM transacao WHERE id_categoria=? AND id_usuario=? LIMIT 1', (cid, uid))
    if em_uso:
        return jsonify(ok=False, msg='Categoria em uso por transações. Reatribua-as antes de excluir.')
    execute('DELETE FROM categoria WHERE id_categoria=? AND id_usuario=?', (cid, uid))
    return jsonify(ok=True)
