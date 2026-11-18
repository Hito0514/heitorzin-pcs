<?php
// Restaura as linhas digitadas caso o servidor tenha rejeitado o pedido
$antigos = [];
foreach ((array)old('produto_id', []) as $k => $pid) {
    if ($pid !== '') {
        $antigos[] = ['produto_id' => (string)$pid, 'quantidade' => (int)((array)old('quantidade', []))[$k] ?: 1];
    }
}
?>
<div class="admin-topo">
    <h1>Novo pedido</h1>
    <a class="btn btn-outline" href="<?= url('admin/pedidos') ?>">&larr; Voltar</a>
</div>

<div class="caixa-form" style="max-width:900px">
    <form method="post" action="<?= url('admin/pedidos/salvar') ?>" id="form-pedido">
        <?= csrf_field() ?>
        <div class="form-grade">
            <div class="campo">
                <label for="cliente_id">Cliente *</label>
                <select id="cliente_id" name="cliente_id" required>
                    <option value="">Selecione...</option>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (string)old('cliente_id') === (string)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?> (<?= e($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="status">Status</label>
                <select id="status" name="status">
                    <?php foreach ($statusLista as $s): ?>
                        <option value="<?= e($s) ?>" <?= old('status', 'pendente') === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo largo">
                <label for="observacao">Observação</label>
                <input type="text" id="observacao" name="observacao" maxlength="255" value="<?= e(old('observacao')) ?>">
            </div>
        </div>

        <h3>Itens do pedido</h3>
        <div id="itens-pedido" data-antigos="<?= e(json_encode($antigos)) ?>"></div>
        <button type="button" class="btn btn-outline btn-sm" id="add-linha">+ Adicionar item</button>

        <div class="total-linha">Total: <strong id="total-pedido">R$ 0,00</strong></div>
        <p class="muted small">O estoque dos produtos é baixado automaticamente ao salvar (exceto se o status for "cancelado").</p>

        <div style="display:flex;gap:.6rem">
            <button class="btn" type="submit">Registrar pedido</button>
            <a class="btn btn-outline" href="<?= url('admin/pedidos') ?>">Cancelar</a>
        </div>
    </form>
</div>

<template id="modelo-linha">
    <div class="pedido-linha">
        <select name="produto_id[]" aria-label="Produto">
            <option value="">Selecione o produto...</option>
            <?php foreach ($produtos as $p): ?>
                <option value="<?= (int)$p['id'] ?>" data-preco="<?= e($p['preco']) ?>" data-estoque="<?= (int)$p['estoque'] ?>" <?= (int)$p['estoque'] <= 0 ? 'disabled' : '' ?>>
                    <?= e($p['nome']) ?> — <?= dinheiro($p['preco']) ?> (estoque: <?= (int)$p['estoque'] ?>)
                </option>
            <?php endforeach; ?>
        </select>
        <input type="number" name="quantidade[]" value="1" min="1" aria-label="Quantidade">
        <span class="sub">R$ 0,00</span>
        <button type="button" class="btn btn-perigo btn-sm remover-linha" aria-label="Remover item">&times;</button>
    </div>
</template>
