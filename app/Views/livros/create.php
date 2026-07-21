<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;">Novo Livro</h1>

<form method="POST" action="/livros" style="margin-top: 20px; max-width: 500px;">
    <div class="field-group">
        <label for="titulo">Título</label>
        <input type="text" id="titulo" name="titulo" required>
    </div>
    <div class="field-group">
        <label for="autor">Autor</label>
        <input type="text" id="autor" name="autor" required>
    </div>
    <div class="field-group">
        <label for="categoria">Categoria</label>
        <input type="text" id="categoria" name="categoria">
    </div>
    <div class="field-group">
        <label for="ano">Ano de publicação</label>
        <input type="number" id="ano" name="ano">
    </div>

    <button type="submit" class="btn btn-green" style="border:none; cursor:pointer;">Salvar</button>
</form>

<p style="margin-top: 16px; color: #888; font-size: 12px;">
    * O envio do formulário já está roteado para LivroController@store, mas o salvamento
    real no banco será implementado na Entrega Parcial 3/4.
</p>
