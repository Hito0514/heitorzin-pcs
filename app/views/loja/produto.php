<div class="container">
    <div class="breadcrumb">
        <a href="<?= url() ?>">Início</a> /
        <a href="<?= url('loja') ?>">Catálogo</a> /
        <a href="<?= url('loja', ['categoria' => $p['categoria_id']]) ?>"><?= e($p['categoria']) ?></a> /
        <?= e($p['nome']) ?>
    </div>

    <div class="produto-layout">
        <div class="produto-img">
            <img src="<?= e(imagem_produto($p['imagem'])) ?>" alt="<?= e($p['nome']) ?>">
        </div>
        <div class="produto-info">
            <span class="card-cat"><?= e($p['categoria']) ?></span>
            <h1><?= e($p['nome']) ?></h1>
            <div class="preco"><?= dinheiro($p['preco']) ?></div>

            <?php $est = (int)$p['estoque']; ?>
            <p>
                <?php if ($est <= 0): ?>
                    <span class="estoque-zero">Produto esgotado</span>
                <?php elseif ($est <= 5): ?>
                    <span class="estoque-baixo">Restam apenas <?= $est ?> unidade(s)!</span>
                <?php else: ?>
                    <span class="estoque-ok">Em estoque (<?= $est ?> unidades)</span>
                <?php endif; ?>
            </p>

            <?php if ($est > 0): ?>
            <form method="post" action="<?= url('carrinho/adicionar') ?>" class="comprar-linha">
                <?= csrf_field() ?>
                <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                <input type="number" name="quantidade" value="1" min="1" max="<?= $est ?>" aria-label="Quantidade">
                <button class="btn" type="submit" style="flex:1">Adicionar ao carrinho</button>
            </form>
            <?php endif; ?>

            <table class="tabela-specs">
                <tr><th>Marca</th><td><?= e($p['marca'] ?: '—') ?></td></tr>
                <tr><th>Categoria</th><td><?= e($p['categoria']) ?></td></tr>
                <tr><th>Código</th><td>#<?= (int)$p['id'] ?></td></tr>
            </table>

            <h3>Descrição</h3>
            <p><?= nl2br(e($p['descricao'] ?: 'Sem descrição cadastrada.')) ?></p>
        </div>
    </div>

    <?php if ($relacionados): ?>
        <div class="secao-titulo"><h2>Mais em <?= e($p['categoria']) ?></h2></div>
        <div class="grade">
            <?php foreach ($relacionados as $p): include __DIR__ . '/_card.php'; endforeach; ?>
        </div>
    <?php endif; ?>
</div>
