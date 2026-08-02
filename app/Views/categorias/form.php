<?php $isEdicao = !empty($categoria); ?>

<a href="/categorias">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= $isEdicao ? 'Editar Categoria' : 'Nova Categoria' ?></h1>

<?php if (!empty($errors)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    <ul style="margin-left: 18px;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="<?= $isEdicao ? '/categorias/' . $categoria['id'] : '/categorias' ?>" style="margin-top: 20px; max-width: 420px;">
    <?php if ($isEdicao): ?>
        <input type="hidden" name="_method" value="PUT">
    <?php endif; ?>

    <div class="field-group">
        <label for="nome">Nome *</label>
        <input type="text" id="nome" name="nome"
               value="<?= htmlspecialchars($old['nome'] ?? $categoria['nome'] ?? '') ?>" required>
    </div>

    <button type="submit" class="btn btn-green" style="border:none; cursor:pointer;">Salvar</button>
</form>
