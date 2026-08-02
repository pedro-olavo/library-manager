<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Autores</h1>
    <a href="/autores/novo" class="btn btn-green">+ Novo Autor</a>
</div>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#dcf3e0; color:#1f7a34; border-radius:4px; font-size:14px;">
    <?php
        $mensagens = [
            'criado'     => 'Autor cadastrado com sucesso!',
            'atualizado' => 'Autor atualizado com sucesso!',
            'excluido'   => 'Autor excluído com sucesso!',
        ];
        echo htmlspecialchars($mensagens[$success] ?? 'Operação realizada com sucesso!');
    ?>
</div>
<?php endif; ?>

<?php if (empty($autores)): ?>
    <p style="margin-top: 20px; color:#666;">Nenhum autor cadastrado ainda.</p>
<?php else: ?>
<table>
    <tr>
        <th>Nome</th>
        <th>Nacionalidade</th>
        <th></th>
    </tr>
    <?php foreach ($autores as $autor): ?>
    <tr>
        <td><?= htmlspecialchars($autor['nome']) ?></td>
        <td><?= htmlspecialchars($autor['nacionalidade'] ?? '—') ?></td>
        <td>
            <a href="/autores/<?= $autor['id'] ?>/editar">Editar</a>
            &nbsp;|&nbsp;
            <form method="POST" action="/autores/<?= $autor['id'] ?>" style="display:inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir este autor?');">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" style="border:none; background:none; color:#a12727; cursor:pointer; padding:0; font-size:13px; text-decoration:underline;">Excluir</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<p style="margin-top: 16px;"><a href="/livros">&larr; Voltar para Livros</a></p>
