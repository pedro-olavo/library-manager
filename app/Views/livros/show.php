<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= htmlspecialchars($livro['titulo']) ?></h1>

<table style="margin-top: 20px;">
    <tr><th>Autor</th><td><?= htmlspecialchars($livro['autor']) ?></td></tr>
    <tr><th>Categoria</th><td><?= htmlspecialchars($livro['categoria']) ?></td></tr>
    <tr><th>Ano de publicação</th><td><?= htmlspecialchars((string) $livro['ano']) ?></td></tr>
    <tr><th>Status</th><td><span class="tag"><?= htmlspecialchars($livro['status']) ?></span></td></tr>
</table>
