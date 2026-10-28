<?php $itens = $estoque->itens(); ?>
<h1 class="h3 mb-4">Movimentar</h1>

<form method="post" class="card card-body formulario">
    <?= csrf() ?>
    <div class="row g-3">
        <div class="col-12">
            <span class="form-label d-block">Tipo</span>
            <div class="btn-group" role="group">
                <input type="radio" class="btn-check" name="tipo" id="saida" value="saida"<?= antigo('tipo', 'saida') === 'saida' ? ' checked' : '' ?>>
                <label class="btn btn-outline-dark" for="saida">Saída</label>
                <input type="radio" class="btn-check" name="tipo" id="entrada" value="entrada"<?= antigo('tipo') === 'entrada' ? ' checked' : '' ?>>
                <label class="btn btn-outline-dark" for="entrada">Entrada</label>
            </div>
        </div>
        <div class="col-md-9">
            <label class="form-label" for="item">Item</label>
            <select class="form-select" id="item" name="item" required>
                <option value="">Selecione</option>
                <?php foreach ($itens as $i): ?>
                    <option value="<?= e($i['id']) ?>"<?= antigo('item') === $i['id'] ? ' selected' : '' ?>><?= e($i['codigo'] . ' · ' . $i['nome']) ?> (saldo <?= $i['quantidade'] ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label" for="quantidade">Quantidade</label>
            <input class="form-control" id="quantidade" name="quantidade" type="number" min="1" value="<?= antigo('quantidade', 1) ?>" required>
        </div>
        <div class="col-md-6">
            <label class="form-label" for="destino">Destino <small class="text-secondary">obrigatório na saída</small></label>
            <input class="form-control" id="destino" name="destino" value="<?= antigo('destino') ?>" placeholder="Pessoa, setor ou chamado">
        </div>
        <div class="col-md-6">
            <label class="form-label" for="observacao">Observação</label>
            <input class="form-control" id="observacao" name="observacao" value="<?= antigo('observacao') ?>" placeholder="Nota fiscal, patrimônio...">
        </div>
    </div>
    <div class="mt-3">
        <button class="btn btn-dark" name="acao" value="movimentar">Registrar</button>
    </div>
</form>
