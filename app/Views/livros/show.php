<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= htmlspecialchars($livro['titulo']) ?></h1>

<table style="margin-top: 20px;">
    <tr><th>Autor</th><td><?= htmlspecialchars($livro['autor'] ?? '—') ?></td></tr>
    <tr><th>Categoria</th><td><?= htmlspecialchars($livro['categoria'] ?? '—') ?></td></tr>
    <tr><th>Editora</th><td><?= htmlspecialchars($livro['editora'] ?? '—') ?></td></tr>
    <tr><th>ISBN</th><td><?= htmlspecialchars($livro['isbn'] ?? '—') ?></td></tr>
    <tr><th>Ano de publicação</th><td><?= htmlspecialchars((string) ($livro['ano_publicacao'] ?? '—')) ?></td></tr>
</table>
