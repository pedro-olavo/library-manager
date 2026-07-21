<h1>Bem-vindo(a) ao Sistema de Biblioteca</h1>
<p style="margin-top: 10px; color: #555;">
    Painel inicial da aplicação. Os números abaixo ainda são estáticos e serão conectados
    ao banco de dados PostgreSQL a partir da Entrega Parcial 3.
</p>

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
