<?php

declare(strict_types=1);

require __DIR__ . '/../src/Armazem.php';
require __DIR__ . '/../src/Estoque.php';
require __DIR__ . '/../src/Usuarios.php';
require __DIR__ . '/../src/web.php';

session_start(['cookie_httponly' => true, 'cookie_samesite' => 'Lax']);

$armazem = new Armazem(getenv('ESTOQUE_DADOS') ?: __DIR__ . '/../dados');
$estoque = new Estoque($armazem);
$usuarios = new Usuarios($armazem);

$pagina = $_GET['p'] ?? 'painel';
$usuario = $_SESSION['usuario'] ?? null;

if ($usuarios->vazio()) {
    $pagina = 'primeiro-acesso';
} elseif ($usuario === null && $pagina !== 'login') {
    redirecionar('login');
}

$admin = ($usuario['perfil'] ?? '') === 'admin';
$somenteAdmin = ['item', 'usuarios'];
if (in_array($pagina, $somenteAdmin, true) && !$admin) {
    redirecionar('painel');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    validarCsrf();
    $acao = $_POST['acao'] ?? '';

    try {
        switch ($acao) {
            case 'primeiro-acesso':
                if (!$usuarios->vazio()) {
                    redirecionar('login');
                }
                $usuarios->criar($_POST['login'], $_POST['nome'], 'admin', $_POST['senha']);
                avisar('Administrador criado. Faça login.');
                redirecionar('login');

            case 'login':
                $logado = $usuarios->autenticar($_POST['login'] ?? '', $_POST['senha'] ?? '');
                if ($logado === null) {
                    throw new DomainException('Login ou senha incorretos.');
                }
                session_regenerate_id(true);
                $_SESSION['usuario'] = $logado;
                redirecionar('painel');

            case 'salvar-item':
                exigirAdmin($admin);
                $estoque->salvarItem($_POST['id'] ?: null, $_POST);
                avisar('Item salvo.');
                redirecionar('itens');

            case 'excluir-item':
                exigirAdmin($admin);
                $estoque->excluirItem($_POST['id']);
                avisar('Item excluído.');
                redirecionar('itens');

            case 'movimentar':
                $estoque->movimentar(
                    $_POST['item'],
                    $_POST['tipo'],
                    (int) $_POST['quantidade'],
                    $usuario['login'],
                    $_POST['destino'] ?? '',
                    $_POST['observacao'] ?? ''
                );
                avisar($_POST['tipo'] === 'entrada' ? 'Entrada registrada.' : 'Saída registrada.');
                redirecionar('movimentar');

            case 'criar-usuario':
                exigirAdmin($admin);
                $usuarios->criar($_POST['login'], $_POST['nome'], $_POST['perfil'], $_POST['senha']);
                avisar('Usuário criado.');
                redirecionar('usuarios');

            case 'perfil-usuario':
                exigirAdmin($admin);
                $usuarios->alterarPerfil($_POST['login'], $_POST['perfil']);
                avisar('Perfil alterado.');
                redirecionar('usuarios');

            case 'excluir-usuario':
                exigirAdmin($admin);
                if ($_POST['login'] === $usuario['login']) {
                    throw new DomainException('Você não pode excluir o próprio usuário.');
                }
                $usuarios->excluir($_POST['login']);
                avisar('Usuário excluído.');
                redirecionar('usuarios');

            case 'minha-senha':
                if ($usuarios->autenticar($usuario['login'], $_POST['atual'] ?? '') === null) {
                    throw new DomainException('Senha atual incorreta.');
                }
                if (($_POST['nova'] ?? '') !== ($_POST['confirmacao'] ?? '')) {
                    throw new DomainException('A confirmação não confere.');
                }
                $usuarios->alterarSenha($usuario['login'], $_POST['nova']);
                avisar('Senha alterada.');
                redirecionar('conta');

            case 'sair':
                session_destroy();
                redirecionar('login');
        }
    } catch (DomainException $e) {
        avisar($e->getMessage(), 'danger');
        $_SESSION['formulario'] = array_diff_key($_POST, array_flip(['senha', 'atual', 'nova', 'confirmacao', 'csrf']));
        redirecionar($pagina, array_intersect_key($_GET, ['id' => 1]));
    }
}

$views = [
    'primeiro-acesso' => 'Primeiro acesso',
    'login' => 'Entrar',
    'painel' => 'Painel',
    'itens' => 'Itens',
    'item' => 'Item',
    'movimentar' => 'Movimentar',
    'historico' => 'Histórico',
    'usuarios' => 'Usuários',
    'conta' => 'Minha conta',
];

if (!isset($views[$pagina])) {
    http_response_code(404);
    $pagina = 'painel';
}

renderizar($pagina, $views[$pagina], compact('estoque', 'usuarios', 'usuario', 'admin'));
