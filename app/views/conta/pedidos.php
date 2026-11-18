<div class="container secao">
    <div class="secao-titulo"><h2>Meus pedidos</h2></div>
    <?php if (!$pedidos): ?>
        <div class="center" style="padding:3rem 0">
            <h3>Você ainda não fez nenhum pedido</h3>
            <a class="btn" href="<?= url('loja') ?>">Ver catálogo</a>
        </div>
    <?php else: ?>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead><tr><th>Pedido</th><th>Data</th><th>Status</th><th>Total</th><th></th></tr></thead>
                <tbody>
                <?php foreach ($pedidos as $p): ?>
                    <tr>
                        <td>#<?= (int)$p['id'] ?></td>
                        <td><?= data_br($p['criado_em'], true) ?></td>
                        <td><?= badge_status($p['status']) ?></td>
                        <td><strong><?= dinheiro($p['total']) ?></strong></td>
                        <td class="right"><a class="btn btn-sm" href="<?= url('conta/pedido/' . (int)$p['id']) ?>">Detalhes</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
