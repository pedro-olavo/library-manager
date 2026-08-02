<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($title) ? htmlspecialchars($title) . ' — ' : '' ?>Sistema de Biblioteca</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; }
        body { background: #eef0f2; color: #222; }
        header.topbar {
            background: #2b3a55; color: #fff; padding: 14px 30px;
            display: flex; align-items: center; justify-content: space-between;
        }
        header.topbar a { color: #fff; text-decoration: none; font-weight: bold; font-size: 18px; }
        nav a { color: #dce3f0; text-decoration: none; margin-left: 20px; font-size: 14px; }
        nav a:hover { text-decoration: underline; }
        main { max-width: 1000px; margin: 30px auto; background: #fff; border: 1px solid #ccc; border-radius: 6px; padding: 30px; }
        footer { text-align: center; color: #888; font-size: 12px; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 14px; }
        th { background: #2b3a55; color: #fff; text-align: left; padding: 8px; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        .btn { display: inline-block; background: #2b3a55; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-size: 13px; }
        .btn-green { background: #2f7a3c; }
        .tag { padding: 2px 8px; border-radius: 10px; background: #dce8ff; color: #204a87; font-size: 12px; }
        .field-group { margin-bottom: 16px; }
        .field-group label { display: block; font-size: 13px; margin-bottom: 4px; }
        .field-group input, .field-group select { width: 100%; padding: 8px; border: 1px solid #999; border-radius: 4px; }
    </style>
</head>
<body>
    <header class="topbar">
        <a href="/">Sistema de Biblioteca</a>
        <nav>
            <a href="/">Início</a>
            <a href="/livros">Livros</a>
            <a href="/autores">Autores</a>
            <a href="/categorias">Categorias</a>
            <a href="/editoras">Editoras</a>
        </nav>
    </header>

    <main>
        <?= $content ?>
    </main>

    <footer>
        Sistema de Biblioteca &mdash; UNIVASF / FACAPE &mdash; 2026.1
    </footer>
</body>
</html>
