<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> · Estoque TI</title>
    <link href="vendor/bootstrap/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
</head>
<body>
<?php if ($usuario): ?>
<nav class="navbar navbar-expand-md navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="?p=painel">Estoque TI</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu" aria-label="Menu">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <?php foreach (['painel' => 'Painel', 'itens' => 'Itens', 'movimentar' => 'Movimentar', 'historico' => 'Histórico'] + ($admin ? ['usuarios' => 'Usuários'] : []) as $p => $rotulo): ?>
                    <li class="nav-item"><a class="nav-link<?= $view === $p ? ' active' : '' ?>" href="?p=<?= $p ?>"><?= $rotulo ?></a></li>
                <?php endforeach; ?>
            </ul>
            <div class="d-flex align-items-center gap-3">
                <a class="nav-link text-white-50" href="?p=conta"><?= e($usuario['nome']) ?></a>
                <form method="post"><?= csrf() ?><button name="acao" value="sair" class="btn btn-sm btn-outline-light">Sair</button></form>
            </div>
        </div>
    </div>
</nav>
<?php endif; ?>

<main class="container py-4<?= $usuario ? '' : ' acesso' ?>">
    <?php if ($aviso): ?>
        <div class="alert alert-<?= e($aviso['tipo']) ?>"><?= e($aviso['mensagem']) ?></div>
    <?php endif; ?>
    <?php require $conteudo; ?>
</main>

<script src="vendor/bootstrap/bootstrap.bundle.min.js"></script>
</body>
</html>
