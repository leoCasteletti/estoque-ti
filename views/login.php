<form method="post" class="card card-body">
    <?= csrf() ?>
    <h1 class="h4 mb-3">Estoque TI</h1>
    <label class="form-label" for="login">Login</label>
    <input class="form-control mb-3" id="login" name="login" value="<?= antigo('login') ?>" required autofocus autocomplete="username">
    <label class="form-label" for="senha">Senha</label>
    <input class="form-control mb-3" id="senha" name="senha" type="password" required autocomplete="current-password">
    <button class="btn btn-dark" name="acao" value="login">Entrar</button>
</form>
