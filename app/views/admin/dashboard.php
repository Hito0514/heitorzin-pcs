<?php $maxCat = $porCategoria ? max(array_column($porCategoria, 'receita')) : 0; ?>
<div class="admin-topo">
    <h1>Dashboard</h1>
    <div class="admin-topo-acoes">
        <a class="btn" href="<?= url('admin/pedidos/criar') ?>">+ Novo pedido</a>
        <a class="btn btn-outline" href="<?= url('admin/relatorios') ?>">Relatório de vendas</a>
    </div>
</div>

<div class="cards-resumo">
    <div class="resumo preto"><span class="rot">Faturamento (sem cancelados)</span><span class="num"><?= dinheiro($resumo['faturamento']) ?></span></div>
    <div class="resumo"><span class="rot">Pedidos</span><span class="num"><?= (int)$resumo['pedidos'] ?></span></div>
    <div class="resumo"><span class="rot">Pedidos pendentes</span><span class="num"><?= (int)$resumo['pendentes'] ?></span></div>
    <div class="resumo"><span class="rot">Produtos</span><span class="num"><?= (int)$totalProdutos ?></span></div>
    <div class="resumo"><span class="rot">Clientes</span><span class="num"><?= (int)$totalClientes ?></span></div>
</div>

<div class="duas-colunas">
    <div class="painel">
        <h3>Últimos pedidos</h3>
        <?php if ($ultimos): ?>
        <ul class="lista-simples">
            <?php foreach ($ultimos as $p): ?>
                <li>
                    <span><a href="<?= url('admin/pedidos/ver/' . (int)$p['id']) ?>">#<?= (int)$p['id'] ?></a> &middot; <?= e($p['cliente']) ?></span>
                    <span><?= badge_status($p['status']) ?> <strong><?= dinheiro($p['total']) ?></strong></span>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?><p class="muted">Nenhum pedido ainda.</p><?php endif; ?>
    </div>

    <div class="painel">
        <h3>Estoque baixo (5 ou menos)</h3>
        <?php if ($estoqueBaixo): ?>
        <ul class="lista-simples">
            <?php foreach ($estoqueBaixo as $p): ?>
                <li>
                    <a href="<?= url('admin/produtos/editar/' . (int)$p['id']) ?>"><?= e($p['nome']) ?></a>
                    <strong class="<?= (int)$p['estoque'] === 0 ? 'estoque-zero' : 'estoque-baixo' ?>"><?= (int)$p['estoque'] ?> un.</strong>
                </li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?><p class="muted">Tudo certo com o estoque.</p><?php endif; ?>
    </div>
</div>

<div class="painel">
    <h3>Receita por categoria (últimos 12 meses)</h3>
    <?php if ($porCategoria): foreach ($porCategoria as $c): ?>
        <div class="barra-linha">
            <div class="topo"><span><?= e($c['nome']) ?> <span class="muted">(<?= (int)$c['quantidade'] ?> un.)</span></span><strong><?= dinheiro($c['receita']) ?></strong></div>
            <div class="barra"><span style="width:<?= $maxCat > 0 ? round($c['receita'] / $maxCat * 100) : 0 ?>%"></span></div>
        </div>
    <?php endforeach; else: ?><p class="muted">Sem vendas no período.</p><?php endif; ?>
</div>
