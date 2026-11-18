<?php
/** Listagem genérica de CRUD. Variáveis: $titulo, $recurso, $singular, $colunas, $registros */
$formatar = function (array $col, array $row): string {
    $valor = $row[$col['key']] ?? null;
    $fmt = $col['fmt'] ?? 'text';
    switch ($fmt) {
        case 'money': return e(dinheiro($valor));
        case 'date':  return e(data_br($valor));
        case 'bool':  return $valor ? '<span class="badge badge-sim">Sim</span>' : '<span class="badge badge-nao">Não</span>';
        case 'image': return '<img class="miniatura" src="' . e(imagem_produto($valor)) . '" alt="">';
        default:      return e($valor === null || $valor === '' ? '—' : $valor);
    }
};
?>
<div class="admin-topo">
    <h1><?= e($titulo) ?></h1>
    <div class="admin-topo-acoes">
        <input type="text" class="filtro-tabela" placeholder="Filtrar na tabela..." data-filtro-tabela="#tabela-crud" aria-label="Filtrar">
        <a class="btn" href="<?= url("admin/$recurso/criar") ?>">+ Novo(a) <?= e($singular) ?></a>
    </div>
</div>

<div class="tabela-wrap">
    <table class="tabela" id="tabela-crud">
        <thead>
            <tr>
                <?php foreach ($colunas as $c): ?><th><?= e($c['label']) ?></th><?php endforeach; ?>
                <th class="right">Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($registros as $r): ?>
            <tr>
                <?php foreach ($colunas as $c): ?><td><?= $formatar($c, $r) ?></td><?php endforeach; ?>
                <td>
                    <div class="acoes">
                        <a class="btn btn-sm btn-outline" href="<?= url("admin/$recurso/editar/" . (int)$r['id']) ?>">Editar</a>
                        <form class="inline-form" method="post" action="<?= url("admin/$recurso/excluir/" . (int)$r['id']) ?>"
                              data-confirm="Excluir este(a) <?= e($singular) ?>? Esta ação não pode ser desfeita.">
                            <?= csrf_field() ?>
                            <button class="btn btn-sm btn-perigo" type="submit">Excluir</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        <?php if (!$registros): ?>
            <tr><td colspan="<?= count($colunas) + 1 ?>" class="center muted" style="padding:2rem">Nenhum registro cadastrado.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>
<p class="muted small"><?= count($registros) ?> registro(s)</p>
