<?php $maxCat = $porCategoria ? max(array_column($porCategoria, 'receita')) : 0; ?>
<div class="admin-topo">
    <h1>Relatório de vendas</h1>
    <div class="admin-topo-acoes">
        <a class="btn btn-outline" href="<?= url('admin/relatorios', ['de' => $de, 'ate' => $ate, 'status' => $status, 'exportar' => 1]) ?>">Exportar CSV</a>
        <button class="btn btn-outline" type="button" onclick="window.print()">Imprimir</button>
    </div>
</div>

<form class="filtros" method="get" action="<?= BASE_URL ?>index.php">
    <input type="hidden" name="url" value="admin/relatorios">
    <div class="campo"><label for="de">De</label><input type="date" id="de" name="de" value="<?= e($de) ?>"></div>
    <div class="campo"><label for="ate">Até</label><input type="date" id="ate" name="ate" value="<?= e($ate) ?>"></div>
    <div class="campo">
        <label for="status">Status</label>
        <select id="status" name="status">
            <option value="">Todos</option>
            <?php foreach ($statusLista as $s): ?>
                <option value="<?= e($s) ?>" <?= $status === $s ? 'selected' : '' ?>><?= e(ucfirst($s)) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="campo"><button class="btn" type="submit">Filtrar</button></div>
</form>

<div class="cards-resumo">
    <div class="resumo preto"><span class="rot">Faturamento no período</span><span class="num"><?= dinheiro($faturamento) ?></span></div>
    <div class="resumo"><span class="rot">Pedidos válidos</span><span class="num"><?= (int)$qtdValidos ?></span></div>
    <div class="resumo"><span class="rot">Ticket médio</span><span class="num"><?= dinheiro($qtdValidos ? $faturamento / $qtdValidos : 0) ?></span></div>
</div>
<p class="muted small">Pedidos cancelados não entram no faturamento.</p>

<div class="duas-colunas">
    <div class="painel">
        <h3>Produtos mais vendidos</h3>
        <?php if ($maisVendidos): ?>
        <ul class="lista-simples">
            <?php foreach ($maisVendidos as $m): ?>
                <li><span><?= e($m['nome']) ?></span><span><strong><?= (int)$m['quantidade'] ?> un.</strong> &middot; <?= dinheiro($m['receita']) ?></span></li>
            <?php endforeach; ?>
        </ul>
        <?php else: ?><p class="muted">Sem vendas no período.</p><?php endif; ?>
    </div>
    <div class="painel">
        <h3>Receita por categoria</h3>
        <?php foreach ($porCategoria as $c): ?>
            <div class="barra-linha">
                <div class="topo"><span><?= e($c['nome']) ?></span><strong><?= dinheiro($c['receita']) ?></strong></div>
                <div class="barra"><span style="width:<?= $maxCat > 0 ? round($c['receita'] / $maxCat * 100) : 0 ?>%"></span></div>
            </div>
        <?php endforeach; ?>
        <?php if (!$porCategoria): ?><p class="muted">Sem vendas no período.</p><?php endif; ?>
    </div>
</div>

<h3>Pedidos do período</h3>
<div class="tabela-wrap">
    <table class="tabela">
        <thead><tr><th>#</th><th>Data</th><th>Cliente</th><th>Status</th><th>Total</th></tr></thead>
        <tbody>
        <?php foreach ($vendas as $v): ?>
            <tr>
                <td><a href="<?= url('admin/pedidos/ver/' . (int)$v['id']) ?>">#<?= (int)$v['id'] ?></a></td>
                <td><?= data_br($v['criado_em'], true) ?></td>
                <td><?= e($v['cliente']) ?></td>
                <td><?= badge_status($v['status']) ?></td>
                <td><strong><?= dinheiro($v['total']) ?></strong></td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$vendas): ?><tr><td colspan="5" class="center muted" style="padding:2rem">Nenhum pedido no período.</td></tr><?php endif; ?>
        </tbody>
    </table>
</div>
