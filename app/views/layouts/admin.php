<?php
$u = Auth::usuario();
$atual = explode('/', trim($_GET['url'] ?? 'admin', '/'))[1] ?? 'dashboard';
$menu = [
    'dashboard'    => 'Dashboard',
    'produtos'     => 'Produtos',
    'categorias'   => 'Categorias',
    'fornecedores' => 'Fornecedores',
    'clientes'     => 'Clientes',
    'pedidos'      => 'Pedidos',
    'relatorios'   => 'Relatórios',
];
if (Auth::ehAdmin()) {
    $menu['usuarios'] = 'Usuários';
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | Admin <?= e(APP_NAME) ?></title>
    <link rel="icon" type="image/svg+xml" href="<?= asset('img/logo.svg') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>">
</head>
<body class="admin">
<div class="admin-layout">
    <aside class="sidebar">
        <a class="marca" href="<?= url('admin') ?>">
            <img src="<?= asset('img/logo.svg') ?>" alt="Logo">
            <span>Heitorzin<br>Admin</span>
        </a>
        <nav>
            <?php foreach ($menu as $rota => $rotulo): ?>
                <a href="<?= url($rota === 'dashboard' ? 'admin' : "admin/$rota") ?>" class="<?= $atual === $rota ? 'ativo' : '' ?>"><?= e($rotulo) ?></a>
            <?php endforeach; ?>
            <a href="<?= url() ?>" target="_blank" rel="noopener">Ver loja &#8599;</a>
        </nav>
        <div class="usuario">
            Logado como<br><strong><?= e($u['nome']) ?></strong><br>
            <span class="muted" style="color:var(--cinza-3)"><?= e(ucfirst($u['perfil'])) ?></span>
            <form method="post" action="<?= url('logout') ?>">
                <?= csrf_field() ?>
                <button class="btn btn-claro btn-sm btn-bloco" type="submit">Sair</button>
            </form>
        </div>
    </aside>
    <div class="admin-main">
        <?php foreach (pegar_flash() as $f): ?>
            <div class="flash flash-<?= e($f['tipo']) ?>" role="alert" style="margin-bottom:1rem"><span><?= e($f['msg']) ?></span><button type="button" aria-label="Fechar">&times;</button></div>
        <?php endforeach; ?>
        <?= $conteudo ?>
    </div>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
