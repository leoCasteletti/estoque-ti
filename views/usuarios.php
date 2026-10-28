<h1 class="h3 mb-4">Usuários</h1>

<div class="table-responsive mb-4">
    <table class="table align-middle">
        <thead><tr><th>Login</th><th>Nome</th><th>Perfil</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($usuarios->todos() as $u): ?>
            <tr>
                <td><code><?= e($u['login']) ?></code></td>
                <td><?= e($u['nome']) ?></td>
                <td>
                    <form method="post" class="d-flex gap-2">
                        <?= csrf() ?>
                        <input type="hidden" name="login" value="<?= e($u['login']) ?>">
                        <select class="form-select form-select-sm w-auto" name="perfil">
                            <?php foreach (Usuarios::PERFIS as $valor => $rotulo): ?>
                                <option value="<?= $valor ?>"<?= $u['perfil'] === $valor ? ' selected' : '' ?>><?= $rotulo ?></option>
                            <?php endforeach; ?>
                        </select>
                        <button class="btn btn-sm btn-outline-secondary" name="acao" value="perfil-usuario">Alterar</button>
                    </form>
                </td>
                <td class="text-end">
                    <?php if ($u['login'] !== $usuario['login']): ?>
                        <form method="post"><?= csrf() ?><input type="hidden" name="login" value="<?= e($u['login']) ?>"><button class="btn btn-sm btn-outline-danger" name="acao" value="excluir-usuario">Excluir</button></form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<form method="post" class="card card-body formulario">
    <?= csrf() ?>
    <h2 class="h5">Novo usuário</h2>
    <div class="row g-3">
        <div class="col-md-6"><label class="form-label" for="nome">Nome</label><input class="form-control" id="nome" name="nome" value="<?= antigo('nome') ?>" required></div>
        <div class="col-md-6"><label class="form-label" for="login">Login</label><input class="form-control" id="login" name="login" value="<?= antigo('login') ?>" required></div>
        <div class="col-md-6">
            <label class="form-label" for="perfil">Perfil</label>
            <select class="form-select" id="perfil" name="perfil">
                <?php foreach (Usuarios::PERFIS as $valor => $rotulo): ?><option value="<?= $valor ?>"><?= $rotulo ?></option><?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-6"><label class="form-label" for="senha">Senha inicial</label><input class="form-control" id="senha" name="senha" type="password" minlength="8" required autocomplete="new-password"></div>
    </div>
    <div class="mt-3"><button class="btn btn-dark" name="acao" value="criar-usuario">Criar usuário</button></div>
</form>
