<?php

declare(strict_types=1);

require __DIR__ . '/../src/Armazem.php';
require __DIR__ . '/../src/Estoque.php';
require __DIR__ . '/../src/Usuarios.php';

$pasta = sys_get_temp_dir() . '/estoque-teste-' . bin2hex(random_bytes(4));
$armazem = new Armazem($pasta);
$estoque = new Estoque($armazem);
$usuarios = new Usuarios($armazem);

$total = 0;
$falhas = 0;
$ok = function (string $nome, bool $condicao) use (&$total, &$falhas): void {
    $total++;
    if (!$condicao) {
        $falhas++;
        echo "FALHOU: $nome\n";
    }
};
$erro = function (string $nome, callable $f, string $trecho) use ($ok): void {
    try {
        $f();
        $ok($nome, false);
    } catch (DomainException $e) {
        $ok($nome, str_contains($e->getMessage(), $trecho));
    }
};

$estoque->salvarItem(null, ['codigo' => 'mou-usb', 'nome' => 'Mouse', 'categoria' => 'Periféricos', 'minimo' => 5]);
$id = $estoque->itens()[0]['id'];
$ok('código em maiúsculo', $estoque->item($id)['codigo'] === 'MOU-USB');
$ok('item nasce zerado e crítico', $estoque->resumo() === ['total' => 1, 'critico' => 1, 'zerado' => 1, 'unidades' => 0]);

$estoque->movimentar($id, 'entrada', 10, 'ana');
$estoque->movimentar($id, 'saida', 3, 'ana', 'Financeiro');
$ok('saldo após movimentos', $estoque->item($id)['quantidade'] === 7);
$ok('deixa de ser crítico', $estoque->resumo()['critico'] === 0);
$ok('histórico mais recente primeiro', $estoque->historico()[0]['tipo'] === 'saida');
$ok('filtro do histórico', count($estoque->historico('financeiro')) === 1);

$erro('saldo insuficiente', fn () => $estoque->movimentar($id, 'saida', 8, 'ana', 'TI'), 'Saldo insuficiente');
$ok('saldo intacto após erro', $estoque->item($id)['quantidade'] === 7);
$erro('saída sem destino', fn () => $estoque->movimentar($id, 'saida', 1, 'ana'), 'para onde');
$erro('quantidade zero', fn () => $estoque->movimentar($id, 'entrada', 0, 'ana'), 'maior que zero');
$erro('código duplicado', fn () => $estoque->salvarItem(null, ['codigo' => 'MOU-USB', 'nome' => 'Outro']), 'já está em uso');
$erro('excluir com saldo', fn () => $estoque->excluirItem($id), 'Zere o estoque');

$usuarios->criar('Admin', 'Administrador', 'admin', 'senha-forte');
$ok('login normalizado', $usuarios->autenticar('admin', 'senha-forte')['perfil'] === 'admin');
$ok('senha errada', $usuarios->autenticar('admin', 'errada') === null);
$ok('senha não é exposta', !isset($usuarios->todos()[0]['senha']));
$erro('senha curta', fn () => $usuarios->criar('bia', 'Bia', 'tecnico', '123'), '8 caracteres');
$erro('último admin', fn () => $usuarios->alterarPerfil('admin', 'tecnico'), 'pelo menos um administrador');

array_map('unlink', glob("$pasta/*.json"));
rmdir($pasta);

echo ($total - $falhas) . "/$total testes passaram\n";
exit($falhas > 0 ? 1 : 0);
