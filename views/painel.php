<?php $r = $estoque->resumo(); $criticos = $estoque->criticos(); ?>
<h1 class="h3 mb-4">Painel</h1>

<div class="row g-3 mb-4 resumo">
    <div class="col-6 col-lg-3"><div class="card card-body"><span><?= $r['total'] ?></span>itens cadastrados</div></div>
    <div class="col-6 col-lg-3"><div class="card card-body"><span><?= $r['unidades'] ?></span>unidades em estoque</div></div>
    <div class="col-6 col-lg-3"><div class="card card-body alerta"><span><?= $r['critico'] ?></span>no estoque mínimo</div></div>
    <div class="col-6 col-lg-3"><div class="card card-body alerta"><span><?= $r['zerado'] ?></span>zerados</div></div>
</div>

<div class="d-flex justify-content-between align-items-center mb-2">
    <h2 class="h5 mb-0">Precisa repor</h2>
    <a class="btn btn-sm btn-dark" href="?p=movimentar">Registrar movimentação</a>
</div>

<?php if ($criticos === []): ?>
    <p class="text-secondary">Nenhum item abaixo do mínimo.</p>
<?php else: ?>
    <div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><th>Código</th><th>Item</th><th>Categoria</th><th class="text-end">Saldo</th><th class="text-end">Mínimo</th></tr></thead>
            <tbody>
            <?php foreach ($criticos as $i): ?>
                <tr>
                    <td><code><?= e($i['codigo']) ?></code></td>
                    <td><?= e($i['nome']) ?></td>
                    <td><?= e($i['categoria']) ?></td>
                    <td class="text-end fw-semibold <?= $i['quantidade'] === 0 ? 'text-danger' : 'text-warning-emphasis' ?>"><?= $i['quantidade'] ?></td>
                    <td class="text-end"><?= $i['minimo'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
