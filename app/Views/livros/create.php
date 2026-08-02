<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;">Novo Livro</h1>

<?php if (!empty($errors)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    <ul style="margin-left: 18px;">
        <?php foreach ($errors as $error): ?>
            <li><?= htmlspecialchars($error) ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="/livros" style="margin-top: 20px; max-width: 560px;">
    <div class="field-group">
        <label for="titulo">Título *</label>
        <input type="text" id="titulo" name="titulo" value="<?= htmlspecialchars($old['titulo'] ?? '') ?>" required>
    </div>

    <div class="field-group">
        <label for="isbn">ISBN</label>
        <input type="text" id="isbn" name="isbn" value="<?= htmlspecialchars($old['isbn'] ?? '') ?>">
    </div>

    <div style="display:flex; gap:20px;">
        <div class="field-group" style="flex:1;">
            <label for="autor_id">Autor *</label>
            <select id="autor_id" name="autor_id">
                <option value="">Selecione...</option>
                <?php foreach ($autores as $autor): ?>
                    <option value="<?= $autor['id'] ?>" <?= (($old['autor_id'] ?? '') == $autor['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($autor['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field-group" style="flex:1;">
            <label for="categoria_id">Categoria</label>
            <select id="categoria_id" name="categoria_id">
                <option value="">Selecione...</option>
                <?php foreach ($categorias as $categoria): ?>
                    <option value="<?= $categoria['id'] ?>" <?= (($old['categoria_id'] ?? '') == $categoria['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($categoria['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div style="display:flex; gap:20px;">
        <div class="field-group" style="flex:1;">
            <label for="editora_id">Editora</label>
            <select id="editora_id" name="editora_id">
                <option value="">Selecione...</option>
                <?php foreach ($editoras as $editora): ?>
                    <option value="<?= $editora['id'] ?>" <?= (($old['editora_id'] ?? '') == $editora['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($editora['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="field-group" style="flex:1;">
            <label for="ano_publicacao">Ano de publicação</label>
            <input type="number" id="ano_publicacao" name="ano_publicacao" value="<?= htmlspecialchars($old['ano_publicacao'] ?? '') ?>">
        </div>
    </div>

    <button type="submit" class="btn btn-green" style="border:none; cursor:pointer; margin-top: 10px;">Salvar</button>
</form>

<p style="margin-top: 16px; color: #888; font-size: 12px;">
    Não encontrou o autor, a categoria ou a editora que precisa?
    Cadastre em <a href="/autores">Autores</a>, <a href="/categorias">Categorias</a> ou <a href="/editoras">Editoras</a>.
</p>
