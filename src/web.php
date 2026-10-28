<?php

declare(strict_types=1);

function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES, 'UTF-8');
}

/** @param array<string, string> $parametros */
function redirecionar(string $pagina, array $parametros = []): never
{
    header('Location: ?' . http_build_query(['p' => $pagina] + $parametros));
    exit;
}

function avisar(string $mensagem, string $tipo = 'success'): void
{
    $_SESSION['aviso'] = ['mensagem' => $mensagem, 'tipo' => $tipo];
}

function csrf(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));

    return '<input type="hidden" name="csrf" value="' . $_SESSION['csrf'] . '">';
}

function validarCsrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(400);
        exit('Sessão expirada. Volte e tente de novo.');
    }
}

function exigirAdmin(bool $admin): void
{
    if (!$admin) {
        throw new DomainException('Ação restrita a administradores.');
    }
}

function antigo(string $campo, mixed $padrao = ''): string
{
    return e($_SESSION['formulario_atual'][$campo] ?? $padrao);
}

function dataBr(string $data): string
{
    return date('d/m/Y H:i', strtotime($data));
}

/** @param array<string, mixed> $dados */
function renderizar(string $view, string $titulo, array $dados): void
{
    $aviso = $_SESSION['aviso'] ?? null;
    $_SESSION['formulario_atual'] = $_SESSION['formulario'] ?? [];
    unset($_SESSION['aviso'], $_SESSION['formulario']);

    extract($dados);
    $conteudo = __DIR__ . "/../views/$view.php";
    require __DIR__ . '/../views/layout.php';
}
