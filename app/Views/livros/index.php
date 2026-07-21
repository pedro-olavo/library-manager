<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Livros</h1>
    <a href="/livros/novo" class="btn btn-green">+ Novo Livro</a>
</div>

<table>
    <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoria</th>
        <th>Ano</th>
        <th>Status</th>
        <th></th>
    </tr>
    <?php foreach ($livros as $livro): ?>
    <tr>
        <td><?= htmlspecialchars($livro['titulo']) ?></td>
        <td><?= htmlspecialchars($livro['autor']) ?></td>
        <td><?= htmlspecialchars($livro['categoria']) ?></td>
        <td><?= htmlspecialchars((string) $livro['ano']) ?></td>
        <td><span class="tag"><?= htmlspecialchars($livro['status']) ?></span></td>
        <td><a href="/livros/<?= $livro['id'] ?>">Ver detalhes</a></td>
    </tr>
    <?php endforeach; ?>
</table>

<p style="margin-top: 16px; color: #888; font-size: 12px;">
    * Dados ilustrativos. A listagem passará a vir do banco PostgreSQL a partir da Entrega Parcial 3.
</p>
