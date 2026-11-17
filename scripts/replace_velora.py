import os

files = [
    'pages/cadastro.html','pages/configuracoes.html','pages/contas.html',
    'pages/dashboard.html','pages/faturas.html','pages/login.html',
    'pages/planejamento.html','pages/transacoes.html',
    'js/apple-indicator.js','js/velora.js',
    'css/cadastro.css','css/dashboard.css','css/responsivo.css'
]

replacements = [
    ('VELORA \u2014 ', 'PAI PEDE \u2014 '),
    ('\u00a9 2025 VELORA', '\u00a9 2025 PAI PEDE'),
    ('Na VELORA,', 'No PAI PEDE,'),
    ('VELORA - ', 'PAI PEDE - '),
    ('Entre na sua conta VELORA.', 'Entre na sua conta PAI PEDE.'),
    ('hillary@velora.app', 'usuario@paipede.app'),
    ('velora123', 'paipede123'),
]

for f in files:
    txt = open(f, encoding='utf-8').read()
    for old, new in replacements:
        txt = txt.replace(old, new)
    open(f, 'w', encoding='utf-8').write(txt)
    n = txt.count('VELORA') + txt.count('velora')
    print(f'{f}: {n} ocorrencias restantes')
