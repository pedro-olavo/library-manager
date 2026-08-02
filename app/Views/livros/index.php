<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Livros</h1>
    <a href="/livros/novo" class="btn btn-green">+ Novo Livro</a>
</div>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#dcf3e0; color:#1f7a34; border-radius:4px; font-size:14px;">
    Livro cadastrado com sucesso!
</div>
<?php endif; ?>

<?php if (empty($livros)): ?>
    <p style="margin-top: 20px; color:#666;">Nenhum livro cadastrado ainda.</p>
<?php else: ?>
<table>
    <tr>
        <th>Título</th>
        <th>Autor</th>
        <th>Categoria</th>
        <th>Editora</th>
        <th>Ano</th>
        <th></th>
    </tr>
    <?php foreach ($livros as $livro): ?>
    <tr>
        <td><?= htmlspecialchars($livro['titulo']) ?></td>
        <td><?= htmlspecialchars($livro['autor'] ?? '—') ?></td>
        <td><?= htmlspecialchars($livro['categoria'] ?? '—') ?></td>
        <td><?= htmlspecialchars($livro['editora'] ?? '—') ?></td>
        <td><?= htmlspecialchars((string) ($livro['ano_publicacao'] ?? '—')) ?></td>
        <td><a href="/livros/<?= $livro['id'] ?>">Ver detalhes</a></td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
