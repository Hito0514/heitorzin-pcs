<div class="admin-topo">
    <h1>Pedidos</h1>
    <div class="admin-topo-acoes">
        <input type="text" class="filtro-tabela" placeholder="Filtrar na tabela..." data-filtro-tabela="#tabela-pedidos" aria-label="Filtrar">
        <a class="btn" href="<?= url('admin/pedidos/criar') ?>">+ Novo pedido</a>
    </div>
</div>

<div class="tabela-wrap">
    <table class="tabela" id="tabela-pedidos">
        <thead><tr><th>#</th><th>Data</th><th>Cliente</th><th>Status</th><th>Total</th><th class="right">Ações</th></tr></thead>
        <tbody>
        <?php foreach ($pedidos as $p): ?>
            <tr>
                <td>#<?= (int)$p['id'] ?></td>
                <td><?= data_br($p['criado_em'], true) ?></td>
                <td><?= e($p['cliente']) ?></td>
                <td><?= badge_status($p['status']) ?></td>
                <td><strong><?= dinheiro($p['total']) ?></strong></td>
                <td>
                    <div class="acoes">
                        <a class="btn btn-sm" href="<?= url('admin/pedidos/ver/' . (int)$p['id']) ?>">Ver</a>
                        <a class="btn btn-sm btn-outline" href="<?= url('admin/pedidos/editar/' . (int)$p['id']) ?>">Editar</a>
                        <form class="inline-form" method="post" action="<?= url('admin/pedidos/excluir/' . (int)$p['id']) ?>"
                              data-confirm="Excluir o pedido #<?= (int)$p['id'] ?>? O estoque dos itens será devolvido.">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-perigo" type="submit">Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$pedidos): ?>
            <tr><td colspan="6" class="center muted" style="padding:2rem">Nenhum pedido cadastrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
