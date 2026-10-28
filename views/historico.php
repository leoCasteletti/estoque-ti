<?php
$busca = $_GET['busca'] ?? '';
$tipo = $_GET['tipo'] ?? '';
$movimentos = $estoque->historico($busca, $tipo);
?>
<h1 class="h3 mb-3">Histórico</h1>

<form class="row g-2 mb-3">
    <input type="hidden" name="p" value="historico">
    <div class="col-md-6"><input class="form-control" name="busca" value="<?= e($busca) ?>" placeholder="Item, destino ou usuário"></div>
    <div class="col-md-4">
        <select class="form-select" name="tipo">
            <option value="">Entradas e saídas</option>
            <option value="entrada"<?= $tipo === 'entrada' ? ' selected' : '' ?>>Só entradas</option>
            <option value="saida"<?= $tipo === 'saida' ? ' selected' : '' ?>>Só saídas</option>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-outline-dark">Filtrar</button></div>
</form>

<div class="table-responsive">
    <table class="table table-sm align-middle">
        <thead><tr><th>Data</th><th>Tipo</th><th>Item</th><th class="text-end">Qtd.</th><th>Destino</th><th>Observação</th><th>Usuário</th></tr></thead>
        <tbody>
        <?php foreach ($movimentos as $m): ?>
            <tr>
                <td class="text-nowrap"><?= dataBr($m['data']) ?></td>
                <td><span class="badge <?= $m['tipo'] === 'entrada' ? 'text-bg-success' : 'text-bg-secondary' ?>"><?= $m['tipo'] === 'entrada' ? 'Entrada' : 'Saída' ?></span></td>
                <td><?= e($m['item']) ?></td>
                <td class="text-end"><?= $m['quantidade'] ?></td>
                <td><?= e($m['destino']) ?></td>
                <td class="text-secondary"><?= e($m['observacao']) ?></td>
                <td><?= e($m['usuario']) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if ($movimentos === []): ?>
            <tr><td colspan="7" class="text-secondary">Nenhuma movimentação.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
