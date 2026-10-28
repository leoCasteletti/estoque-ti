<h1 class="h3 mb-4">Minha conta</h1>

<form method="post" class="card card-body formulario">
    <?= csrf() ?>
    <p class="mb-3"><?= e($usuario['nome']) ?> · <code><?= e($usuario['login']) ?></code> · <?= Usuarios::PERFIS[$usuario['perfil']] ?></p>
    <label class="form-label" for="atual">Senha atual</label>
    <input class="form-control mb-3" id="atual" name="atual" type="password" required autocomplete="current-password">
    <label class="form-label" for="nova">Nova senha</label>
    <input class="form-control mb-3" id="nova" name="nova" type="password" minlength="8" required autocomplete="new-password">
    <label class="form-label" for="confirmacao">Confirmar nova senha</label>
    <input class="form-control mb-3" id="confirmacao" name="confirmacao" type="password" minlength="8" required autocomplete="new-password">
    <div><button class="btn btn-dark" name="acao" value="minha-senha">Alterar senha</button></div>
</form>
