<form method="post" class="card card-body">
    <?= csrf() ?>
    <h1 class="h4">Primeiro acesso</h1>
    <p class="text-secondary">Crie o usuário administrador.</p>
    <label class="form-label" for="nome">Nome</label>
    <input class="form-control mb-3" id="nome" name="nome" value="<?= antigo('nome') ?>" required>
    <label class="form-label" for="login">Login</label>
    <input class="form-control mb-3" id="login" name="login" value="<?= antigo('login') ?>" required autocomplete="username">
    <label class="form-label" for="senha">Senha <small class="text-secondary">mínimo 8 caracteres</small></label>
    <input class="form-control mb-3" id="senha" name="senha" type="password" minlength="8" required autocomplete="new-password">
    <button class="btn btn-dark" name="acao" value="primeiro-acesso">Criar administrador</button>
</form>
