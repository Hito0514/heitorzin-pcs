<div class="admin-topo">
    <h1>Pedido #<?= (int)$pedido['id'] ?> <?= badge_status($pedido['status']) ?></h1>
    <div class="admin-topo-acoes">
        <a class="btn btn-outline" href="<?= url('admin/pedidos/editar/' . (int)$pedido['id']) ?>">Editar status</a>
        <a class="btn btn-outline" href="<?= url('admin/pedidos') ?>">&larr; Voltar</a>
    </div>
</div>

<div class="duas-colunas">
    <div class="painel">
        <h3>Cliente</h3>
        <p style="margin:0"><strong><?= e($pedido['cliente']) ?></strong><br>
            <?= e($pedido['cliente_email']) ?><br><?= e($pedido['cliente_telefone']) ?></p>
    </div>
    <div class="painel">
        <h3>Informações</h3>
        <p style="margin:0">Data: <?= data_br($pedido['criado_em'], true) ?><br>
            Observação: <?= e($pedido['observacao'] ?: '—') ?></p>
    </div>
</div>

<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>Produto</th><th>Preço unit.</th><th>Qtd.</th><th>Subtotal</th></tr></thead>
        <tbody>
        <?php foreach ($itens as $i): ?>
            <tr>
                <td><?= e($i['produto']) ?></td>
                <td><?= dinheiro($i['preco_unitario']) ?></td>
                <td><?= (int)$i['quantidade'] ?></td>
                <td><?= dinheiro($i['quantidade'] * $i['preco_unitario']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<div class="total-linha">Total: <strong><?= dinheiro($pedido['total']) ?></strong></div>
