<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Usuários</h1>
    <a href="/usuarios/novo" class="btn btn-green">+ Novo Usuário</a>
</div>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:<?= $success === 'erro_autoexclusao' ? '#ffdada' : '#dcf3e0' ?>; color:<?= $success === 'erro_autoexclusao' ? '#a12727' : '#1f7a34' ?>; border-radius:4px; font-size:14px;">
    <?php
        $mensagens = [
            'criado'             => 'Usuário cadastrado com sucesso!',
            'atualizado'         => 'Usuário atualizado com sucesso!',
            'excluido'           => 'Usuário excluído com sucesso!',
            'erro_autoexclusao'  => 'Você não pode excluir a própria conta enquanto está logado.',
        ];
        echo htmlspecialchars($mensagens[$success] ?? 'Operação realizada com sucesso!');
    ?>
</div>
<?php endif; ?>

<table>
    <tr>
        <th>Nome</th>
        <th>E-mail</th>
        <th>Perfil</th>
        <th></th>
    </tr>
    <?php foreach ($usuarios as $usuario): ?>
    <tr>
        <td><?= htmlspecialchars($usuario['nome']) ?></td>
        <td><?= htmlspecialchars($usuario['email']) ?></td>
        <td><span class="tag"><?= htmlspecialchars($usuario['perfil']) ?></span></td>
        <td>
            <a href="/usuarios/<?= $usuario['id'] ?>/editar">Editar</a>
            &nbsp;|&nbsp;
            <form method="POST" action="/usuarios/<?= $usuario['id'] ?>" style="display:inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir este usuário?');">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" style="border:none; background:none; color:#a12727; cursor:pointer; padding:0; font-size:13px; text-decoration:underline;">Excluir</button>
            </form>
        </td>
    </tr>
    <?php endforeach; ?>
</table>

<p style="margin-top: 16px;"><a href="/">&larr; Voltar para o início</a></p>
