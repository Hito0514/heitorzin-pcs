<div class="container secao">
    <div class="secao-titulo"><h2>Seu carrinho</h2></div>

    <?php if (!$itens): ?>
        <div class="center" style="padding:3rem 0">
            <h3>Seu carrinho está vazio</h3>
            <p class="muted">Que tal dar uma olhada nas nossas peças?</p>
            <a class="btn" href="<?= url('loja') ?>">Ir para o catálogo</a>
        </div>
    <?php else: ?>
        <form method="post" action="<?= url('carrinho/atualizar') ?>">
            <?= csrf_field() ?>
            <div class="tabela-wrap">
                <table class="tabela">
                    <thead><tr><th>Produto</th><th>Preço</th><th>Qtd.</th><th>Subtotal</th><th></th></tr></thead>
                    <tbody>
                    <?php foreach ($itens as $i): $p = $i['produto']; ?>
                        <tr>
                            <td>
                                <div style="display:flex;gap:.8rem;align-items:center">
                                    <img class="miniatura" src="<?= e(imagem_produto($p['imagem'])) ?>" alt="">
                                    <a href="<?= url('produto/' . (int)$p['id']) ?>"><strong><?= e($p['nome']) ?></strong></a>
                                </div>
                            </td>
                            <td><?= dinheiro($p['preco']) ?></td>
                            <td><input type="number" name="qtd[<?= (int)$p['id'] ?>]" value="<?= (int)$i['quantidade'] ?>" min="0" max="<?= (int)$p['estoque'] ?>"></td>
                            <td><strong><?= dinheiro($i['subtotal']) ?></strong></td>
                            <td class="right">
                                <button class="btn btn-perigo btn-sm" type="submit" form="remover-<?= (int)$p['id'] ?>">Remover</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div style="margin-top:1rem;display:flex;gap:.6rem;flex-wrap:wrap">
                <button class="btn btn-outline" type="submit">Atualizar quantidades</button>
                <a class="btn btn-outline" href="<?= url('loja') ?>">Continuar comprando</a>
            </div>
        </form>

        <?php foreach ($itens as $i): ?>
            <form id="remover-<?= (int)$i['produto']['id'] ?>" method="post" action="<?= url('carrinho/remover') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="produto_id" value="<?= (int)$i['produto']['id'] ?>">
            </form>
        <?php endforeach; ?>

        <div class="total-linha">Total: <strong><?= dinheiro($total) ?></strong></div>

        <form method="post" action="<?= url('carrinho/finalizar') ?>" style="text-align:right">
            <?= csrf_field() ?>
            <?php if (!Auth::logado()): ?>
                <p class="muted">Para finalizar, <a href="<?= url('login') ?>"><strong>entre</strong></a> ou <a href="<?= url('registro') ?>"><strong>crie sua conta</strong></a>.</p>
            <?php endif; ?>
            <button class="btn" type="submit" style="font-size:1.2rem;padding:.9rem 2rem">Finalizar pedido</button>
        </form>
    <?php endif; ?>
</div>
