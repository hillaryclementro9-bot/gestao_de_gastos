const express  = require('express');
const Database = require('better-sqlite3');
const bcrypt   = require('bcryptjs');
const cors     = require('cors');
const path     = require('path');

const app = express();
const db  = new Database(path.join(__dirname, 'database', 'paipede.db'));

// Criar tabela de usuários
db.exec(`
  CREATE TABLE IF NOT EXISTS users (
    id        INTEGER PRIMARY KEY AUTOINCREMENT,
    name      TEXT    NOT NULL,
    email     TEXT    NOT NULL UNIQUE,
    password  TEXT    NOT NULL,
    created_at TEXT   DEFAULT (datetime('now'))
  )
`);

app.use(cors());
app.use(express.json());
app.use(express.static(__dirname));

// ── CADASTRO ──────────────────────────────────────
app.post('/api/cadastro', (req, res) => {
  const { name, email, password } = req.body;

  if (!name || !email || !password) {
    return res.json({ ok: false, msg: 'Preencha todos os campos.' });
  }
  if (password.length < 6) {
    return res.json({ ok: false, msg: 'Senha mínimo 6 caracteres.' });
  }

  const existe = db.prepare('SELECT id FROM users WHERE email = ?').get(email);
  if (existe) {
    return res.json({ ok: false, msg: 'E-mail já cadastrado.' });
  }

  const hash = bcrypt.hashSync(password, 10);
  const info = db.prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)').run(name, email, hash);

  res.json({ ok: true, user: { id: info.lastInsertRowid, name, email } });
});

// ── LOGIN ─────────────────────────────────────────
app.post('/api/login', (req, res) => {
  const { email, password } = req.body;

  if (!email || !password) {
    return res.json({ ok: false, msg: 'Preencha e-mail e senha.' });
  }

  const user = db.prepare('SELECT * FROM users WHERE email = ?').get(email);
  if (!user || !bcrypt.compareSync(password, user.password)) {
    return res.json({ ok: false, msg: 'E-mail ou senha incorretos.' });
  }

  res.json({ ok: true, user: { id: user.id, name: user.name, email: user.email } });
});

// ── ADMIN ─────────────────────────────────────────
app.post('/api/admin', (req, res) => {
  res.json({ ok: true, user: { id: 'admin', name: 'Admin', email: 'admin@paipede.app' } });
});

const PORT = 3000;
app.listen(PORT, () => console.log(`PAI PEDE rodando em http://localhost:${PORT}`));
