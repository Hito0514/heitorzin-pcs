<section class="hero">
    <div class="container">
        <div class="hero-caixa">
            <h1>Heitorzin PCs</h1>
            <p>Processadores, placas de vídeo, memórias, SSDs e tudo que você precisa para montar o PC dos seus sonhos. Do jeito que a massa gosta: <strong>preto no branco</strong>.</p>
            <div class="hero-acoes">
                <a class="btn" href="<?= url('loja') ?>">Ver catálogo</a>
                <a class="btn btn-claro" href="<?= url('registro') ?>">Criar minha conta</a>
            </div>
        </div>
    </div>
</section>

<section class="secao">
    <div class="container">
        <div class="secao-titulo"><h2>Categorias</h2><a href="<?= url('loja') ?>">Ver tudo &rarr;</a></div>
        <div class="chips">
            <?php foreach ($categorias as $c): ?>
                <a class="chip" href="<?= url('loja', ['categoria' => $c['id']]) ?>"><?= e($c['nome']) ?><small>(<?= (int)$c['total_produtos'] ?>)</small></a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php if ($destaques): ?>
<section class="secao secao-escura">
    <div class="container">
        <div class="secao-titulo"><h2>Em destaque</h2></div>
        <div class="grade">
            <?php foreach ($destaques as $p): include __DIR__ . '/_card.php'; endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="secao">
    <div class="container">
        <div class="secao-titulo"><h2>Últimos lançamentos na loja</h2><a href="<?= url('loja') ?>">Catálogo completo &rarr;</a></div>
        <?php if ($recentes): ?>
        <div class="grade">
            <?php foreach ($recentes as $p): include __DIR__ . '/_card.php'; endforeach; ?>
        </div>
        <?php else: ?>
            <p class="muted">Nenhum produto cadastrado ainda.</p>
        <?php endif; ?>
    </div>
</section>

<section class="secao secao-escura">
    <div class="container">
        <div class="valores">
            <div class="valor"><h3>Peças selecionadas</h3><p>Marcas reconhecidas e produtos com boa procedência.</p></div>
            <div class="valor"><h3>Estoque em tempo real</h3><p>Você só compra o que realmente temos disponível.</p></div>
            <div class="valor"><h3>Compra simples</h3><p>Monte o carrinho, finalize o pedido e acompanhe tudo na sua conta.</p></div>
        </div>
    </div>
</section>
