import re

# 1. index.html — remove stats (+12k, 98%, 256-bit)
with open('index.html', 'r', encoding='utf-8') as f:
    txt = f.read()
txt = re.sub(r'\s*<div class="hero-stats">.*?</div>\s*</div>\s*</div>', '\n      </div>\n\n    </div>', txt, flags=re.DOTALL)
with open('index.html', 'w', encoding='utf-8') as f:
    f.write(txt)
print('index.html: stats removidos')

# 2. Sidebar logo em todas as páginas: VEL<span>ORA</span> -> PAI <span>PEDE</span>
pages = ['pages/dashboard.html','pages/transacoes.html','pages/contas.html',
         'pages/faturas.html','pages/planejamento.html','pages/configuracoes.html']

for p in pages:
    with open(p, 'r', encoding='utf-8') as f:
        txt = f.read()
    txt = txt.replace('VEL<span>ORA</span>', 'PAI <span>PEDE</span>')
    with open(p, 'w', encoding='utf-8') as f:
        f.write(txt)
    print(f'{p}: logo corrigido')

# 3. cadastro.html — remove campo confirmar senha
with open('pages/cadastro.html', 'r', encoding='utf-8') as f:
    txt = f.read()

# Remove o grupo do campo confirmar senha
txt = re.sub(
    r'\s*<div class="group">\s*<label>Confirmar senha</label>.*?</div>\s*</div>',
    '',
    txt,
    flags=re.DOTALL
)

# Remove validação blur do confirm
txt = re.sub(r"\s*document\.getElementById\('confirm'\)\.addEventListener\('blur'.*?\}\);\n", '', txt, flags=re.DOTALL)

# Remove validação do confirm no submit
txt = txt.replace(
    "    if (cf !== pw || !cf.length)                    valid = setError('confirm',  'As senhas não coincidem.');   else setOk('confirm');\n",
    ''
)

# Remove declaração da variável cf
txt = txt.replace("    const cf    = document.getElementById('confirm').value;\n", '')

# Remove toggleEye do confirm
txt = txt.replace("  toggleEye('eye-cf', 'confirm');\n", '')

with open('pages/cadastro.html', 'w', encoding='utf-8') as f:
    f.write(txt)
print('pages/cadastro.html: campo confirmar senha removido')
