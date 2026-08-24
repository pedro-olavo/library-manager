<div style="display:flex; justify-content:center; padding: 20px 0;">
    <div style="width:100%; max-width:360px; border:1px solid #ccc; border-radius:6px; padding:30px;">
        <h2 style="text-align:center; color:#2b3a55;">Login</h2>

        <?php if (!empty($error)): ?>
        <div style="margin-top: 16px; padding: 12px 16px; background:#ffdada; color:#a12727; border-radius:4px; font-size:14px;">
            <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <form method="POST" action="/login" style="margin-top: 20px;">
            <div class="field-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="<?= htmlspecialchars($old['email'] ?? '') ?>" required autofocus>
            </div>
            <div class="field-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" class="btn btn-green" style="width:100%; border:none; cursor:pointer; margin-top: 10px;">Entrar</button>
        </form>

        <p style="margin-top: 20px; color:#888; font-size: 12px; text-align:center;">
            Usuários de demonstração (senha entre parênteses):<br>
            administrador@biblioteca.com (admin123)<br>
            bibliotecario@biblioteca.com (biblio123)<br>
            leitor@biblioteca.com (leitor123)
        </p>
    </div>
</div>
