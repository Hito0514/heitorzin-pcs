<?php /** Card de produto. Espera a variável $p. */ ?>
<article class="card">
    <?php if ((int)$p['estoque'] <= 0): ?>
        <span class="selo selo-esgotado">Esgotado</span>
    <?php elseif (!empty($p['destaque'])): ?>
        <span class="selo">Destaque</span>
    <?php endif; ?>
    <a class="card-img" href="<?= url('produto/' . (int)$p['id']) ?>">
        <img src="<?= e(imagem_produto($p['imagem'])) ?>" alt="<?= e($p['nome']) ?>" loading="lazy">
    </a>
    <div class="card-corpo">
        <span class="card-cat"><?= e($p['categoria'] ?? '') ?><?= !empty($p['marca']) ? ' &bull; ' . e($p['marca']) : '' ?></span>
        <a class="card-nome" href="<?= url('produto/' . (int)$p['id']) ?>"><?= e($p['nome']) ?></a>
        <span class="preco"><?= dinheiro($p['preco']) ?></span>
        <div class="card-rodape">
            <?php if ((int)$p['estoque'] > 0): ?>
                <form method="post" action="<?= url('carrinho/adicionar') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="produto_id" value="<?= (int)$p['id'] ?>">
                    <input type="hidden" name="quantidade" value="1">
                    <button class="btn btn-bloco" type="submit">Adicionar ao carrinho</button>
                </form>
            <?php else: ?>
                <a class="btn btn-bloco btn-outline" href="<?= url('produto/' . (int)$p['id']) ?>">Ver detalhes</a>
            <?php endif; ?>
        </div>
    </div>
</article>
