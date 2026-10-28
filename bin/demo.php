<?php

declare(strict_types=1);

require __DIR__ . '/../src/Armazem.php';
require __DIR__ . '/../src/Estoque.php';
require __DIR__ . '/../src/Usuarios.php';

$pasta = $argv[1] ?? __DIR__ . '/../dados';
if (glob("$pasta/*.json")) {
    fwrite(STDERR, "A pasta $pasta já tem dados. Use uma pasta vazia.\n");
    exit(1);
}

$armazem = new Armazem($pasta);
$estoque = new Estoque($armazem);
$usuarios = new Usuarios($armazem);

$usuarios->criar('admin', 'Administrador', 'admin', 'admin1234');
$usuarios->criar('tecnico', 'Técnico de Suporte', 'tecnico', 'tecnico1234');

$itens = [
    ['MOU-USB', 'Mouse USB', 'Periféricos', 10, 24],
    ['TEC-ABNT2', 'Teclado ABNT2', 'Periféricos', 8, 5],
    ['HEADSET', 'Headset com microfone', 'Periféricos', 5, 0],
    ['CABO-HDMI', 'Cabo HDMI 1,8 m', 'Cabos', 10, 31],
    ['CABO-REDE', 'Cabo de rede Cat6 2,5 m', 'Cabos', 20, 12],
    ['SSD-480', 'SSD 480 GB SATA', 'Armazenamento', 3, 6],
    ['MEM-8DDR4', 'Memória 8 GB DDR4', 'Componentes', 4, 2],
    ['TONER-105A', 'Toner 105A', 'Impressão', 6, 9],
    ['FONTE-NB', 'Fonte universal para notebook', 'Energia', 3, 4],
    ['ADAPT-USBC', 'Adaptador USB-C para HDMI', 'Cabos', 4, 1],
];

foreach ($itens as [$codigo, $nome, $categoria, $minimo, $quantidade]) {
    $estoque->salvarItem(null, compact('codigo', 'nome', 'categoria', 'minimo'));
    $id = array_values(array_filter($estoque->itens(), fn ($i) => $i['codigo'] === $codigo))[0]['id'];
    if ($quantidade > 0) {
        $estoque->movimentar($id, 'entrada', $quantidade + 2, 'admin', '', 'Carga inicial');
        $estoque->movimentar($id, 'saida', 2, 'tecnico', 'Chamado 1042', '');
    }
}

echo "Dados de demonstração criados em $pasta\n";
echo "Logins: admin / admin1234 e tecnico / tecnico1234\n";
