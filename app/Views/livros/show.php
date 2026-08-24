<?php $podeGerenciar = in_array(\App\Core\Auth::role(), ['administrador', 'bibliotecario'], true); ?>

<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= htmlspecialchars($livro['titulo']) ?></h1>

<table style="margin-top: 20px;">
    <tr><th>Autor</th><td><?= htmlspecialchars($livro['autor'] ?? '—') ?></td></tr>
    <tr><th>Categoria</th><td><?= htmlspecialchars($livro['categoria'] ?? '—') ?></td></tr>
    <tr><th>Editora</th><td><?= htmlspecialchars($livro['editora'] ?? '—') ?></td></tr>
    <tr><th>ISBN</th><td><?= htmlspecialchars($livro['isbn'] ?? '—') ?></td></tr>
    <tr><th>Ano de publicação</th><td><?= htmlspecialchars((string) ($livro['ano_publicacao'] ?? '—')) ?></td></tr>
</table>

<?php if ($podeGerenciar): ?>
<div style="margin-top: 20px; display:flex; gap:12px;">
    <a href="/livros/<?= $livro['id'] ?>/editar" class="btn">Editar</a>
    <form method="POST" action="/livros/<?= $livro['id'] ?>"
          onsubmit="return confirm('Tem certeza que deseja excluir este livro?');">
        <input type="hidden" name="_method" value="DELETE">
        <button type="submit" style="height:36px; padding:0 16px; background:#a12727; color:#fff; border:none; border-radius:4px; cursor:pointer; font-size:13px;">Excluir</button>
    </form>
</div>
<?php endif; ?>
