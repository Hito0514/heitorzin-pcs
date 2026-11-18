<?php
$qtdCarrinho = array_sum($_SESSION['carrinho'] ?? []);
$logado = Auth::usuario();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | <?= e(APP_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body>
<div class="aviso-topo">Peças para PC &bull; Preto no branco, sem enrolação</div>
<header class="topbar">
    <div class="container topbar-inner">
        <a class="marca" href="<?= url() ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="Logo Heitorzin PCs">
            <span>Heitorzin PCs</span>
        </a>
        <form class="busca-topo" action="<?= BASE_URL ?>index.php" method="get">
            <input type="hidden" name="url" value="loja">
            <input type="search" name="q" placeholder="Buscar peças (ex.: RTX, Ryzen, SSD)" value="<?= e($_GET['q'] ?? '') ?>" aria-label="Buscar produtos">
            <button type="submit">Buscar</button>
        </form>
        <nav class="menu">
            <a href="<?= url() ?>">Início</a>
            <a href="<?= url('loja') ?>">Catálogo</a>
            <a href="<?= url('carrinho') ?>">Carrinho<?php if ($qtdCarrinho): ?><span class="contador"><?= (int)$qtdCarrinho ?></span><?php endif; ?></a>
            <?php if ($logado && $logado['tipo'] === 'usuario'): ?>
                <a href="<?= url('admin') ?>">Painel admin</a>
            <?php elseif ($logado): ?>
                <a href="<?= url('conta') ?>">Meus pedidos</a>
            <?php endif; ?>
            <?php if ($logado): ?>
                <form class="inline-form" method="post" action="<?= url('logout') ?>">
                    <?= csrf_field() ?>
                    <button class="link" type="submit">Sair (<?= e(explode(' ', $logado['nome'])[0]) ?>)</button>
                </form>
            <?php else: ?>
                <a href="<?= url('login') ?>">Entrar</a>
                <a href="<?= url('registro') ?>">Criar conta</a>
            <?php endif; ?>
        </nav>
    </div>
</header>

<?php $flashes = pegar_flash(); if ($flashes): ?>
<div class="container flashes">
    <?php foreach ($flashes as $f): ?>
        <div class="flash flash-<?= e($f['tipo']) ?>" role="alert"><span><?= e($f['msg']) ?></span><button type="button" aria-label="Fechar">&times;</button></div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<main>
<?= $conteudo ?>
</main>

<footer class="rodape">
    <div class="container rodape-inner">
        <div>
            <strong style="font-family:var(--fonte-titulo);font-size:1.3rem;letter-spacing:.06em;">HEITORZIN PCs</strong><br>
            <span class="small">Projeto acadêmico &mdash; loja de peças para computador.</span>
        </div>
        <div class="small">
            Desenvolvido por Heitor Almeida Souza<br>
            PHP 8 &bull; MySQL &bull; PDO &bull; MVC
        </div>
        <div class="small"><a href="<?= url('login') ?>">Área administrativa</a></div>
    </div>
</footer>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
