<div class="container secao">
    <div class="secao-titulo">
        <h2>Pedido #<?= (int)$pedido['id'] ?></h2>
        <?= badge_status($pedido['status']) ?>
    </div>
    <p class="muted">Realizado em <?= data_br($pedido['criado_em'], true) ?></p>
    <?php if ($pedido['observacao']): ?><p><strong>Observação:</strong> <?= e($pedido['observacao']) ?></p><?php endif; ?>

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
    <a class="btn btn-outline" href="<?= url('conta') ?>">&larr; Voltar aos meus pedidos</a>
</div>
