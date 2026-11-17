# PAI PEDE — Sistema de Gestão Financeira

Sistema full-stack de gerenciamento financeiro pessoal com PHP + MySQL.

## Pré-requisitos

- **XAMPP** (Apache + MySQL + PHP 8+) → https://www.apachefriends.org
- Ou **PHP 8+** + **MySQL 8+** instalados separadamente

---

## Instalação com XAMPP (recomendado)

### 1. Copiar o projeto
Copie a pasta `gestao_de_gastos-main` para:
```
C:\xampp\htdocs\paipede\
```

### 2. Criar o banco de dados
1. Abra o **XAMPP Control Panel** e inicie **Apache** e **MySQL**
2. Acesse `http://localhost/phpmyadmin`
3. Clique em **Importar** → selecione o arquivo:
   ```
   database/paipede.sql
   ```
4. Clique em **Executar**

### 3. Configurar a conexão
Abra `php/config.php` e ajuste se necessário:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'paipede');
define('DB_USER', 'root');
define('DB_PASS', '');   // sua senha do MySQL, se tiver
define('DB_PORT', '3306');
```

### 4. Acessar o sistema
Abra o navegador em:
```
http://localhost/paipede/index.html
```

---

## Estrutura do projeto

```
paipede/
├── index.html              ← Página de boas-vindas
├── pages/
│   ├── cadastro.php        ← Login + Cadastro + Recuperar senha
│   ├── login.php           ← Redireciona para cadastro.php
│   ├── dashboard.php       ← Visão geral + Maçã Financeira
│   ├── transacoes.php      ← CRUD de transações
│   ├── contas.php          ← Contas bancárias e cartões
│   ├── faturas.php         ← Faturas e contas a pagar
│   ├── planejamento.php    ← Orçamentos e metas
│   └── configuracoes.php   ← Perfil, senha, categorias
├── php/
│   ├── config.php          ← Conexão MySQL + helpers
│   └── api.php             ← API REST completa
├── database/
│   └── paipede.sql         ← Script SQL para importar
├── css/                    ← Estilos legados
├── js/                     ← Scripts legados
└── img/                    ← Imagens
```

---

## Banco de dados — Tabelas

| Tabela       | Descrição                              |
|--------------|----------------------------------------|
| `usuario`    | Usuários cadastrados                   |
| `categoria`  | Categorias de receita/despesa          |
| `conta`      | Contas bancárias                       |
| `cartao`     | Cartões de crédito                     |
| `fatura`     | Faturas dos cartões                    |
| `transacao`  | Movimentações financeiras              |
| `orcamento`  | Orçamentos mensais por categoria       |
| `meta`       | Metas financeiras com progresso        |

---

## Funcionalidades

- ✅ Cadastro e login com senha criptografada (bcrypt)
- ✅ Recuperação de senha
- ✅ Dashboard com saldo, receitas, despesas e gráfico
- ✅ **Maçã Financeira** — indicador visual automático de saúde financeira
- ✅ CRUD completo de transações com filtros
- ✅ Contas bancárias e cartões de crédito
- ✅ Faturas com alertas de vencimento
- ✅ Orçamentos mensais por categoria
- ✅ Metas financeiras com barra de progresso e depósito
- ✅ Categorias personalizadas
- ✅ Edição de perfil e alteração de senha
- ✅ Layout responsivo (mobile + desktop)
- ✅ Dados isolados por usuário

---

## Maçã Financeira

O indicador funciona automaticamente:

| Situação | Maçã |
|----------|------|
| Gastos < 50% da receita | 🍎 Inteira e vermelha — Excelente! |
| Gastos entre 50–70% | 🍎 Levemente mordida — Atenção |
| Gastos entre 70–90% | 🍎 Mordida — Cuidado! |
| Gastos > 90% | 🍎 Muito mordida — Alerta! |
