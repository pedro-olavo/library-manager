<?php $podeGerenciar = in_array(\App\Core\Auth::role(), ['administrador', 'bibliotecario'], true); ?>

<a href="/livros">&larr; Voltar para a listagem</a>

<h1 style="margin-top: 14px;"><?= htmlspecialchars($livro['titulo']) ?></h1>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#dcf3e0; color:#1f7a34; border-radius:4px; font-size:14px;">
    <?php
        $mensagens = ['exemplar_criado' => 'Exemplar cadastrado com sucesso!', 'exemplar_excluido' => 'Exemplar excluído com sucesso!'];
        echo htmlspecialchars($mensagens[$success] ?? 'Operação realizada com sucesso!');
    ?>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    <?php
        $mensagensErro = ['exemplar_em_uso' => 'Não é possível excluir este exemplar: ele possui empréstimos registrados no histórico.'];
        echo htmlspecialchars($mensagensErro[$error] ?? 'Não foi possível concluir a operação.');
    ?>
</div>
<?php endif; ?>

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

<h2 style="margin-top: 34px; font-size:18px; color:#2b3a55;">Exemplares</h2>

<?php $statusBadges = [
    'disponivel' => ['bg' => '#dcf3e0', 'cor' => '#1f7a34', 'texto' => 'Disponível'],
    'emprestado' => ['bg' => '#dce8ff', 'cor' => '#204a87', 'texto' => 'Emprestado'],
    'reservado'  => ['bg' => '#fff3cd', 'cor' => '#8a6d1a', 'texto' => 'Reservado'],
    'manutencao' => ['bg' => '#eee',    'cor' => '#555',    'texto' => 'Manutenção'],
]; ?>

<?php if (empty($exemplares)): ?>
    <p style="margin-top: 10px; color:#666; font-size:14px;">Nenhum exemplar cadastrado ainda.</p>
<?php else: ?>
<table style="margin-top: 10px;">
    <tr>
        <th>Código de patrimônio</th>
        <th>Status</th>
        <?php if ($podeGerenciar): ?><th></th><?php endif; ?>
    </tr>
    <?php foreach ($exemplares as $exemplar): ?>
    <?php $badge = $statusBadges[$exemplar['status']] ?? ['bg' => '#eee', 'cor' => '#333', 'texto' => $exemplar['status']]; ?>
    <tr>
        <td><?= htmlspecialchars($exemplar['codigo_patrimonio'] ?? '—') ?></td>
        <td><span class="tag" style="background:<?= $badge['bg'] ?>; color:<?= $badge['cor'] ?>;"><?= htmlspecialchars($badge['texto']) ?></span></td>
        <?php if ($podeGerenciar): ?>
        <td>
            <form method="POST" action="/exemplares/<?= $exemplar['id'] ?>" style="display:inline;"
                  onsubmit="return confirm('Tem certeza que deseja excluir este exemplar?');">
                <input type="hidden" name="_method" value="DELETE">
                <button type="submit" style="border:none; background:none; color:#a12727; cursor:pointer; padding:0; font-size:13px; text-decoration:underline;">Excluir</button>
            </form>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>

<?php if ($podeGerenciar): ?>
<form method="POST" action="/livros/<?= $livro['id'] ?>/exemplares" style="margin-top: 16px; display:flex; gap:10px; align-items:flex-end; max-width:420px;">
    <div class="field-group" style="flex:1; margin-bottom:0;">
        <label for="codigo_patrimonio">Novo exemplar — código de patrimônio</label>
        <input type="text" id="codigo_patrimonio" name="codigo_patrimonio" placeholder="Ex: BIB-2026-001">
    </div>
    <button type="submit" class="btn" style="border:none; cursor:pointer; height:34px;">Adicionar</button>
</form>
<?php endif; ?>
