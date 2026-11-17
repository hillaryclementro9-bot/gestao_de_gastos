# PAI PEDE — Sistema de Gestão Financeira

Sistema full-stack com **Python + Flask + SQLite**. Funciona 100% local, sem MySQL, XAMPP ou serviços externos.

---

## Pré-requisitos

- Python 3.8 ou superior → https://www.python.org/downloads/
- VS Code → https://code.visualstudio.com/

---

## Como executar no VS Code

### 1. Abrir o projeto
```
Arquivo → Abrir Pasta → selecione a pasta PaiPede
```

### 2. Abrir o terminal integrado
```
Terminal → Novo Terminal  (ou Ctrl + `)
```

### 3. Criar o ambiente virtual
```bash
python -m venv venv
```

### 4. Ativar o ambiente virtual

**Windows:**
```bash
venv\Scripts\activate
```

**Mac/Linux:**
```bash
source venv/bin/activate
```

### 5. Instalar as dependências
```bash
pip install -r requirements.txt
```

### 6. Executar o sistema
```bash
python app.py
```

### 7. Acessar no navegador
```
http://localhost:5000
```

O banco de dados `database/paipede.db` é criado automaticamente na primeira execução.

---

## Estrutura do projeto

```
PaiPede/
├── app.py                      ← Servidor Flask principal
├── requirements.txt
├── README.md
│
├── database/
│   ├── paipede.db              ← Banco SQLite (criado automaticamente)
│   ├── conexao.py              ← Funções de conexão
│   └── criar_tabelas.py        ← Criação automática das tabelas
│
├── backend/
│   ├── autenticacao.py         ← Login, cadastro, logout
│   ├── transacoes.py           ← CRUD transações + dashboard
│   ├── contas.py               ← CRUD contas bancárias
│   ├── cartoes.py              ← CRUD cartões
│   ├── faturas.py              ← CRUD faturas
│   ├── metas.py                ← CRUD metas + depósito
│   ├── orcamentos.py           ← CRUD orçamentos
│   ├── categorias.py           ← CRUD categorias
│   └── usuarios.py             ← Perfil e senha
│
└── frontend/
    ├── templates/
    │   ├── base.html           ← Layout com sidebar
    │   ├── index.html          ← Boas-vindas (sem menu)
    │   ├── cadastro.html       ← Login + Cadastro
    │   ├── visao_geral.html    ← Dashboard + Maçã Financeira
    │   ├── transacoes.html     ← CRUD transações
    │   ├── cartoes_contas.html ← Contas e cartões
    │   ├── faturas.html        ← Faturas
    │   ├── planejamento.html   ← Orçamentos e metas
    │   └── configuracoes.html  ← Perfil e categorias
    └── static/
        └── css/style.css
```

---

## Tabelas do banco de dados

| Tabela      | Descrição                        |
|-------------|----------------------------------|
| `usuario`   | Usuários cadastrados             |
| `categoria` | Categorias padrão e personalizadas |
| `conta`     | Contas bancárias                 |
| `cartao`    | Cartões de crédito               |
| `fatura`    | Faturas dos cartões              |
| `transacao` | Todas as movimentações           |
| `orcamento` | Orçamentos mensais               |
| `meta`      | Metas financeiras                |

---

## Funcionalidades

- ✅ Cadastro e login com senha criptografada (bcrypt via Werkzeug)
- ✅ Recuperação de senha
- ✅ Dashboard com saldo, receitas, despesas e gráfico (Chart.js)
- ✅ **Maçã Financeira** — indicador visual dinâmico com mordida proporcional
- ✅ CRUD completo de transações com filtros
- ✅ Contas bancárias e cartões com limite/fatura
- ✅ Faturas com alertas de vencimento
- ✅ Orçamentos mensais por categoria com barra de progresso
- ✅ Metas financeiras com depósito e progresso
- ✅ Categorias personalizadas
- ✅ Edição de perfil, senha e preferências
- ✅ Layout responsivo (mobile + desktop)
- ✅ Dados isolados por usuário
- ✅ Banco SQLite criado automaticamente
