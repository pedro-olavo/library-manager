<?php $podeGerenciar = in_array(\App\Core\Auth::role(), ['administrador', 'bibliotecario'], true); ?>

<div style="display:flex; justify-content:space-between; align-items:center;">
    <h1>Empréstimos</h1>
    <?php if ($podeGerenciar): ?>
        <a href="/emprestimos/novo" class="btn btn-green">+ Registrar Empréstimo</a>
    <?php endif; ?>
</div>

<?php if (!empty($success)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#dcf3e0; color:#1f7a34; border-radius:4px; font-size:14px;">
    <?php
        $mensagens = ['registrado' => 'Empréstimo registrado com sucesso!', 'devolvido' => 'Devolução registrada com sucesso!'];
        echo htmlspecialchars($mensagens[$success] ?? 'Operação realizada com sucesso!');
    ?>
</div>
<?php endif; ?>

<?php if (!empty($error)): ?>
<div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
    Não foi possível concluir a operação.
</div>
<?php endif; ?>

<div style="margin-top: 16px; font-size: 13px;">
    <a href="/emprestimos?filtro=ativos" style="<?= $somenteAtivos ? 'font-weight:bold;' : '' ?>">Ativos</a>
    &nbsp;|&nbsp;
    <a href="/emprestimos?filtro=todos" style="<?= !$somenteAtivos ? 'font-weight:bold;' : '' ?>">Todos</a>
</div>

<?php if (empty($emprestimos)): ?>
    <p style="margin-top: 20px; color:#666;">Nenhum empréstimo encontrado.</p>
<?php else: ?>
<table>
    <tr>
        <th>Usuário</th>
        <th>Livro</th>
        <th>Exemplar</th>
        <th>Empréstimo</th>
        <th>Previsão</th>
        <th>Devolução</th>
        <th>Status</th>
        <?php if ($podeGerenciar): ?><th></th><?php endif; ?>
    </tr>
    <?php foreach ($emprestimos as $emp): ?>
    <?php
        $badgeCores = [
            'em_dia'    => ['bg' => '#dce8ff', 'cor' => '#204a87', 'texto' => 'Em dia'],
            'atrasado'  => ['bg' => '#ffdada', 'cor' => '#a12727', 'texto' => 'Atrasado'],
            'devolvido' => ['bg' => '#e6e6e6', 'cor' => '#555',    'texto' => 'Devolvido'],
        ];
        $badge = $badgeCores[$emp['status']] ?? ['bg' => '#eee', 'cor' => '#333', 'texto' => $emp['status']];
    ?>
    <tr>
        <td><?= htmlspecialchars($emp['usuario_nome']) ?></td>
        <td><?= htmlspecialchars($emp['livro_titulo']) ?></td>
        <td><?= htmlspecialchars($emp['codigo_patrimonio'] ?? '—') ?></td>
        <td><?= htmlspecialchars($emp['data_emprestimo']) ?></td>
        <td><?= htmlspecialchars($emp['data_prevista_devolucao']) ?></td>
        <td><?= htmlspecialchars($emp['data_devolucao'] ?? '—') ?></td>
        <td><span class="tag" style="background:<?= $badge['bg'] ?>; color:<?= $badge['cor'] ?>;"><?= htmlspecialchars($badge['texto']) ?></span></td>
        <?php if ($podeGerenciar): ?>
        <td>
            <?php if ($emp['status'] !== 'devolvido'): ?>
            <form method="POST" action="/emprestimos/<?= $emp['id'] ?>/devolver" style="display:inline;"
                  onsubmit="return confirm('Confirmar devolução deste exemplar?');">
                <button type="submit" style="border:1px solid #999; background:#fff; cursor:pointer; padding:4px 10px; border-radius:4px; font-size:12px;">Registrar devolução</button>
            </form>
            <?php endif; ?>
        </td>
        <?php endif; ?>
    </tr>
    <?php endforeach; ?>
</table>
<?php endif; ?>
