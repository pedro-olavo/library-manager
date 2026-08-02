<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Categorias</h1>
    <a href="/categorias/novo" class="btn btn-green">+ Nova Categoria</a>
</div>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#dcf3e0; color:#1f7a34; border-radius:4px; font-size:14px;">
    <?php
        $mensagens = [
            'criada'    => 'Categoria cadastrada com sucesso!',
            'atualizada'=> 'Categoria atualizada com sucesso!',
            'excluida'  => 'Categoria excluída com sucesso!',
        ];
        echo htmlspecialchars($mensagens[$success] ?? 'Operação realizada com sucesso!');
    ?>
</div>
<?php endif; ?>

<?php if (empty($categorias)): ?>
    <p style="margin-top: 20px; color:#666;">Nenhuma categoria cadastrada ainda.</p>
<?php else: ?>
<table>
    <tr>
        <th>Nome</th>
        <th></th>
    </tr>
    <?php foreach ($categorias as $categoria): ?>
    <tr>
        <td><?= htmlspecialchars($categoria['nome']) ?></td>
        <td>
            <a href="/categorias/<?= $categoria['id'] ?>/editar">Editar</a>
            &nbsp;|&nbsp;
            <form method="POST" action="/categorias/<?= $categoria['id'] ?>" style="display:inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir esta categoria?');">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" style="border:none; background:none; color:#a12727; cursor:pointer; padding:0; font-size:13px; text-decoration:underline;">Excluir</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<p style="margin-top: 16px;"><a href="/livros">&larr; Voltar para Livros</a></p>
