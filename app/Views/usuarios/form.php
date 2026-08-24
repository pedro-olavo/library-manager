<?php $isEdicao = !empty($usuario); ?>

<a href="/usuarios">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= $isEdicao ? 'Editar Usuário' : 'Novo Usuário' ?></h1>

<?php if (!empty($errors)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    <ul style="margin-left: 18px;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php $valores = !empty($old) ? $old : ($usuario ?? []); ?>

<form method="POST" action="<?= $isEdicao ? '/usuarios/' . $usuario['id'] : '/usuarios' ?>" style="margin-top: 20px; max-width: 420px;">
    <?php if ($isEdicao): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="field-group">
        <label for="nome">Nome *</label>
        <input type="text" id="nome" name="nome" value="<?= htmlspecialchars($valores['nome'] ?? '') ?>" required>
    </div>

    <div class="field-group">
        <label for="email">E-mail *</label>
        <input type="email" id="email" name="email" value="<?= htmlspecialchars($valores['email'] ?? '') ?>" required>
    </div>

    <div class="field-group">
        <label for="perfil">Perfil *</label>
        <select id="perfil" name="perfil" required>
            <option value="">Selecione...</option>
            <?php foreach (['administrador', 'bibliotecario', 'leitor'] as $perfil): ?>
                <option value="<?= $perfil ?>" <?= (($valores['perfil'] ?? '') === $perfil) ? 'selected' : '' ?>>
                    <?= ucfirst($perfil) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field-group">
        <label for="senha">Senha <?= $isEdicao ? '(deixe em branco para manter a atual)' : '*' ?></label>
        <input type="password" id="senha" name="senha" <?= $isEdicao ? '' : 'required' ?>>
    </div>

    <button type="submit" class="btn btn-green" style="border:none; cursor:pointer;">Salvar</button>
</form>
