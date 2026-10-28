<?php $item = isset($_GET['id']) ? $estoque->item($_GET['id']) : null; ?>
<h1 class="h3 mb-4"><?= $item ? 'Editar item' : 'Novo item' ?></h1>

<form method="post" class="card card-body formulario">
    <?= csrf() ?>
    <input type="hidden" name="id" value="<?= e($item['id'] ?? '') ?>">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label" for="codigo">Código</label>
            <input class="form-control" id="codigo" name="codigo" value="<?= antigo('codigo', $item['codigo'] ?? '') ?>" required>
        </div>
        <div class="col-md-8">
            <label class="form-label" for="nome">Nome</label>
            <input class="form-control" id="nome" name="nome" value="<?= antigo('nome', $item['nome'] ?? '') ?>" required>
        </div>
        <div class="col-md-8">
            <label class="form-label" for="categoria">Categoria</label>
            <input class="form-control" id="categoria" name="categoria" list="categorias" value="<?= antigo('categoria', $item['categoria'] ?? '') ?>">
            <datalist id="categorias">
                <?php foreach ($estoque->categorias() as $c): ?><option value="<?= e($c) ?>"><?php endforeach; ?>
            </datalist>
        </div>
        <div class="col-md-4">
            <label class="form-label" for="minimo">Estoque mínimo</label>
            <input class="form-control" id="minimo" name="minimo" type="number" min="0" value="<?= antigo('minimo', $item['minimo'] ?? 0) ?>">
        </div>
    </div>
    <p class="text-secondary small mt-3 mb-0">O saldo muda só por movimentação, para manter o histórico completo.</p>
    <div class="d-flex gap-2 mt-3">
        <button class="btn btn-dark" name="acao" value="salvar-item">Salvar</button>
        <a class="btn btn-outline-secondary" href="?p=itens">Cancelar</a>
        <?php if ($item): ?>
            <button class="btn btn-outline-danger ms-auto" name="acao" value="excluir-item" formnovalidate>Excluir</button>
        <?php endif; ?>
    </div>
</form>
