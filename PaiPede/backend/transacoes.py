from flask import Blueprint, request, session, jsonify
from database.conexao import query, execute
from functools import wraps

trans_bp = Blueprint('transacoes', __name__)

def login_required(f):
    @wraps(f)
    def decorated(*args, **kwargs):
        if 'user_id' not in session:
            return jsonify(ok=False, msg='Não autenticado.'), 401
        return f(*args, **kwargs)
    return decorated

@trans_bp.route('/api/dashboard')
@login_required
def dashboard():
    uid = session['user_id']
    mes = request.args.get('mes', '')

    filtro_mes = ''
    params_base = [uid]
    if mes:
        filtro_mes = "AND strftime('%Y-%m', data_transacao) = ?"
        params_base = [uid, mes]

    # receitas e despesas
    rows = query(f"""
        SELECT tipo, COALESCE(SUM(valor),0) as total
        FROM transacao
        WHERE id_usuario=? {filtro_mes} AND situacao != 'cancelado'
        GROUP BY tipo
    """, tuple(params_base))
    totais = {'receita': 0.0, 'despesa': 0.0}
    for r in rows:
        totais[r['tipo']] = float(r['total'])

    # saldo = saldo inicial das contas + movimentações pagas
    saldo_contas = query('SELECT COALESCE(SUM(saldo_inicial),0) as s FROM conta WHERE id_usuario=?', (uid,))
    saldo_base = float(saldo_contas[0]['s']) if saldo_contas else 0.0

    adj = query("""
        SELECT COALESCE(SUM(CASE WHEN tipo='receita' THEN valor ELSE -valor END),0) as adj
        FROM transacao WHERE id_usuario=? AND situacao='pago'
    """, (uid,))
    saldo = saldo_base + float(adj[0]['adj'])

    # despesas por categoria
    por_cat = query(f"""
        SELECT c.nome, c.tipo, COALESCE(SUM(t.valor),0) as total
        FROM transacao t
        JOIN categoria c ON c.id_categoria = t.id_categoria
        WHERE t.id_usuario=? AND t.tipo='despesa' {filtro_mes} AND t.situacao!='cancelado'
        GROUP BY t.id_categoria ORDER BY total DESC LIMIT 8
    """, tuple(params_base))

    # próximas contas pendentes
    proximas = query("""
        SELECT descricao, valor, data_vencimento
        FROM transacao
        WHERE id_usuario=? AND situacao='pendente' AND data_vencimento >= date('now')
        ORDER BY data_vencimento ASC LIMIT 5
    """, (uid,))

    # metas
    metas = query('SELECT * FROM meta WHERE id_usuario=? ORDER BY data_limite ASC LIMIT 4', (uid,))

    return jsonify(
        ok=True,
        saldo=round(saldo, 2),
        receitas=round(totais['receita'], 2),
        despesas=round(totais['despesa'], 2),
        por_categoria=por_cat,
        proximas=proximas,
        metas=metas
    )

@trans_bp.route('/api/transacoes', methods=['GET'])
@login_required
def listar():
    uid = session['user_id']
    params = [uid]
    sql = """
        SELECT t.*, c.nome as categoria_nome,
               ct.nome as conta_nome, ca.nome as cartao_nome
        FROM transacao t
        LEFT JOIN categoria c ON c.id_categoria = t.id_categoria
        LEFT JOIN conta ct    ON ct.id_conta    = t.id_conta
        LEFT JOIN cartao ca   ON ca.id_cartao   = t.id_cartao
        WHERE t.id_usuario = ?
    """
    mes = request.args.get('mes')
    tipo = request.args.get('tipo')
    sit  = request.args.get('situacao')
    busca= request.args.get('busca')

    if mes:   sql += " AND strftime('%Y-%m', t.data_transacao)=?"; params.append(mes)
    if tipo:  sql += " AND t.tipo=?";     params.append(tipo)
    if sit:   sql += " AND t.situacao=?"; params.append(sit)
    if busca: sql += " AND t.descricao LIKE ?"; params.append(f'%{busca}%')

    sql += " ORDER BY t.data_transacao DESC, t.id_transacao DESC"
    return jsonify(ok=True, data=query(sql, tuple(params)))

@trans_bp.route('/api/transacoes', methods=['POST'])
@login_required
def criar():
    uid = session['user_id']
    d = request.get_json()
    campos = ['descricao', 'valor', 'tipo', 'data_transacao', 'id_categoria']
    for f in campos:
        if not d.get(f):
            return jsonify(ok=False, msg=f'Campo {f} obrigatório.')
    lid = execute("""
        INSERT INTO transacao
        (id_usuario,id_conta,id_cartao,id_categoria,descricao,valor,tipo,
         data_transacao,data_vencimento,data_efetivacao,situacao,id_fatura)
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?)
    """, (uid, d.get('id_conta'), d.get('id_cartao'), d['id_categoria'],
          d['descricao'], d['valor'], d['tipo'], d['data_transacao'],
          d.get('data_vencimento'), d.get('data_efetivacao'),
          d.get('situacao','pago'), d.get('id_fatura')))
    return jsonify(ok=True, id=lid)

@trans_bp.route('/api/transacoes/<int:tid>', methods=['PUT'])
@login_required
def atualizar(tid):
    uid = session['user_id']
    d = request.get_json()
    execute("""
        UPDATE transacao SET id_conta=?,id_cartao=?,id_categoria=?,descricao=?,valor=?,
        tipo=?,data_transacao=?,data_vencimento=?,data_efetivacao=?,situacao=?
        WHERE id_transacao=? AND id_usuario=?
    """, (d.get('id_conta'), d.get('id_cartao'), d['id_categoria'],
          d['descricao'], d['valor'], d['tipo'], d['data_transacao'],
          d.get('data_vencimento'), d.get('data_efetivacao'),
          d.get('situacao','pago'), tid, uid))
    return jsonify(ok=True)

@trans_bp.route('/api/transacoes/<int:tid>', methods=['DELETE'])
@login_required
def deletar(tid):
    uid = session['user_id']
    execute('DELETE FROM transacao WHERE id_transacao=? AND id_usuario=?', (tid, uid))
    return jsonify(ok=True)
