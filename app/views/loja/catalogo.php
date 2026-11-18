<div class="container secao">
    <div class="secao-titulo"><h2>Catálogo</h2><span class="muted"><?= count($produtos) ?> produto(s) encontrado(s)</span></div>

    <form class="filtros" method="get" action="<?= BASE_URL ?>index.php">
        <input type="hidden" name="url" value="loja">
        <div class="campo">
            <label for="q">Buscar</label>
            <input type="text" id="q" name="q" value="<?= e($busca) ?>" placeholder="Nome, marca ou descrição">
        </div>
        <div class="campo">
            <label for="categoria">Categoria</label>
            <select id="categoria" name="categoria">
                <option value="0">Todas</option>
                <?php foreach ($categorias as $c): ?>
                    <option value="<?= (int)$c['id'] ?>" <?= $cat === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo">
            <label for="ordem">Ordenar por</label>
            <select id="ordem" name="ordem">
                <?php foreach (['recentes' => 'Mais recentes', 'preco_asc' => 'Menor preço', 'preco_desc' => 'Maior preço', 'nome' => 'Nome (A-Z)'] as $v => $r): ?>
                    <option value="<?= $v ?>" <?= $ordem === $v ? 'selected' : '' ?>><?= $r ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo"><button class="btn" type="submit">Filtrar</button></div>
        <div class="campo"><a class="btn btn-outline" href="<?= url('loja') ?>">Limpar</a></div>
    </form>

    <?php if ($produtos): ?>
        <div class="grade">
            <?php foreach ($produtos as $p): include __DIR__ . '/_card.php'; endforeach; ?>
        </div>
    <?php else: ?>
        <div class="center" style="padding:3rem 0">
            <h3>Nenhum produto encontrado</h3>
            <p class="muted">Tente outro termo de busca ou remova os filtros.</p>
            <a class="btn" href="<?= url('loja') ?>">Ver todos os produtos</a>
        </div>
    <?php endif; ?>
</div>
