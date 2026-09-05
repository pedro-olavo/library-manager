<h1>Bem-vindo(a), <?= htmlspecialchars(\App\Core\Auth::user()['nome']) ?>!</h1>

<div style="display: flex; gap: 20px; margin-top: 24px;">
    <div style="flex:1; border:1px solid #ddd; border-radius:6px; padding:20px; text-align:center;">
        <div style="font-size: 28px; font-weight:bold; color:#2b3a55;"><?= $totalLivros ?></div>
        <div style="color:#666; font-size: 13px;">Livros cadastrados</div>
    </div>
    <div style="flex:1; border:1px solid #ddd; border-radius:6px; padding:20px; text-align:center;">
        <div style="font-size: 28px; font-weight:bold; color:#2b3a55;"><?= $totalEmprestimosAtivos ?></div>
        <div style="color:#666; font-size: 13px;">Empréstimos ativos</div>
    </div>
</div>

<div style="margin-top: 30px;">
    <a href="/livros" class="btn">Ver acervo de livros</a>
</div>
