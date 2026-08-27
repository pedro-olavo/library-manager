<a href="/emprestimos">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;">Registrar Empréstimo</h1>

<?php if (!empty($errors)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    <ul style="margin-left: 18px;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<?php if (empty($livrosDisponiveis)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#fff3cd; color:#8a6d1a; border-radius:4px; font-size:14px;">
    Não há livros com exemplares disponíveis para empréstimo no momento. Cadastre
    um exemplar na página de detalhes do livro desejado.
</div>
<?php endif; ?>

<form method="POST" action="/emprestimos" style="margin-top: 20px; max-width: 480px;">
    <div class="field-group">
        <label for="usuario_id">Usuário *</label>
        <select id="usuario_id" name="usuario_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($usuarios as $usuario): ?>
                <option value="<?= $usuario['id'] ?>" <?= (($old['usuario_id'] ?? '') == $usuario['id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($usuario['nome']) ?> (<?= htmlspecialchars($usuario['perfil']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field-group">
        <label for="livro_id">Livro *</label>
        <select id="livro_id" name="livro_id" required>
            <option value="">Selecione...</option>
            <?php foreach ($livrosDisponiveis as $livro): ?>
                <option value="<?= $livro['livro_id'] ?>" <?= (($old['livro_id'] ?? '') == $livro['livro_id']) ? 'selected' : '' ?>>
                    <?= htmlspecialchars($livro['titulo']) ?> (<?= (int) $livro['disponiveis'] ?> disponível<?= $livro['disponiveis'] > 1 ? 'is' : '' ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="field-group">
        <label for="data_prevista_devolucao">Data prevista de devolução *</label>
        <input type="date" id="data_prevista_devolucao" name="data_prevista_devolucao"
               value="<?= htmlspecialchars($old['data_prevista_devolucao'] ?? date('Y-m-d', strtotime('+14 days'))) ?>" required>
    </div>

    <button type="submit" class="btn btn-green" style="border:none; cursor:pointer;">Confirmar Empréstimo</button>
</form>
