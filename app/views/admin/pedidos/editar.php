<div class="admin-topo">
    <h1>Editar pedido #<?= (int)$pedido['id'] ?></h1>
    <a class="btn btn-outline" href="<?= url('admin/pedidos') ?>">&larr; Voltar</a>
</div>

<div class="caixa-form" style="max-width:860px">
    <form method="post" action="<?= url('admin/pedidos/atualizar/' . (int)$pedido['id']) ?>">
        <?= csrf_field() ?>
        <div class="form-grade">
            <div class="campo">
                <label for="cliente_id">Cliente *</label>
                <select id="cliente_id" name="cliente_id" required>
                    <?php foreach ($clientes as $c): ?>
                        <option value="<?= (int)$c['id'] ?>" <?= (int)$pedido['cliente_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?> (<?= e($c['email']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    <?php foreach ($statusLista as $s): ?>
                        <option value="<?= e($s) ?>" <?= $pedido['status'] === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo largo">
                <label for="observacao">Observação</label>
                <input type="text" id="observacao" name="observacao" maxlength="255" value="<?= e($pedido['observacao']) ?>">
            </div>
        </div>

        <h3>Itens (somente leitura)</h3>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead><tr><th>Produto</th><th>Preço unit.</th><th>Qtd.</th><th>Subtotal</th></tr></thead>
                <tbody>
                <?php foreach ($itens as $i): ?>
                    <tr><td><?= e($i['produto']) ?></td><td><?= dinheiro($i['preco_unitario']) ?></td><td><?= (int)$i['quantidade'] ?></td><td><?= dinheiro($i['quantidade'] * $i['preco_unitario']) ?></td></tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <p class="muted small">Dica: marcar como <strong>cancelado</strong> devolve os itens ao estoque. Para alterar os itens, cancele este pedido e crie outro.</p>

        <div style="display:flex;gap:.6rem">
            <button class="btn" type="submit">Salvar alterações</button>
            <a class="btn btn-outline" href="<?= url('admin/pedidos') ?>">Cancelar</a>
        </div>
    </form>
</div>
