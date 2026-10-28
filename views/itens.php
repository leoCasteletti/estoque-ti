<?php
$busca = $_GET['busca'] ?? '';
$categoria = $_GET['categoria'] ?? '';
$itens = $estoque->itens($busca, $categoria);
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h1 class="h3 mb-0">Itens</h1>
    <?php if ($admin): ?><a class="btn btn-dark" href="?p=item">Novo item</a><?php endif; ?>
</div>

<form class="row g-2 mb-3">
    <input type="hidden" name="p" value="itens">
    <div class="col-md-6"><input class="form-control" name="busca" value="<?= e($busca) ?>" placeholder="Buscar por nome ou código"></div>
    <div class="col-md-4">
        <select class="form-select" name="categoria">
            <option value="">Todas as categorias</option>
            <?php foreach ($estoque->categorias() as $c): ?>
                <option<?= $c === $categoria ? ' selected' : '' ?>><?= e($c) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-2 d-grid"><button class="btn btn-outline-dark">Filtrar</button></div>
</form>

<div class="table-responsive">
    <table class="table align-middle">
        <thead><tr><th>Código</th><th>Item</th><th>Categoria</th><th class="text-end">Saldo</th><th class="text-end">Mínimo</th><?php if ($admin): ?><th></th><?php endif; ?></tr></thead>
        <tbody>
        <?php foreach ($itens as $i): ?>
            <tr>
                <td><code><?= e($i['codigo']) ?></code></td>
                <td><?= e($i['nome']) ?></td>
                <td><?= e($i['categoria']) ?></td>
                <td class="text-end<?= $estoque->critico($i) ? ' text-danger fw-semibold' : '' ?>"><?= $i['quantidade'] ?></td>
                <td class="text-end"><?= $i['minimo'] ?></td>
                <?php if ($admin): ?><td class="text-end"><a class="btn btn-sm btn-outline-secondary" href="?p=item&amp;id=<?= e($i['id']) ?>">Editar</a></td><?php endif; ?>
            </tr>
        <?php endforeach; ?>
        <?php if ($itens === []): ?>
            <tr><td colspan="6" class="text-secondary">Nenhum item encontrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
