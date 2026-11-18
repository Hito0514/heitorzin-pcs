<?php
/** Formulário genérico de CRUD. Variáveis: $titulo, $recurso, $singular, $campos, $registro (null = criando) */
$temArquivo = false;
foreach ($campos as $c) { if ($c['type'] === 'file') { $temArquivo = true; } }
$temOld = !empty($_SESSION['old']);
$acao = $registro ? "admin/$recurso/atualizar/" . (int)$registro['id'] : "admin/$recurso/salvar";

/** Valor atual do campo: dados antigos (erro de validação) > registro > padrão. */
$valorDe = function (array $c) use ($registro, $temOld) {
    $n = $c['name'];
    if ($temOld) {
        return $c['type'] === 'checkbox' ? (isset($_SESSION['old'][$n]) ? 1 : 0) : ($_SESSION['old'][$n] ?? '');
    }
    if ($registro) {
        return $registro[$n] ?? '';
    }
    return $c['default'] ?? '';
};
?>
<div class="admin-topo">
    <h1><?= e($titulo) ?></h1>
    <a class="btn btn-outline" href="<?= url("admin/$recurso") ?>">&larr; Voltar</a>
</div>

<div class="caixa-form" style="max-width:860px">
    <form method="post" action="<?= url($acao) ?>" data-validate <?= $temArquivo ? 'enctype="multipart/form-data"' : '' ?>>
        <?= csrf_field() ?>
        <div class="form-grade">
        <?php foreach ($campos as $c):
            $n = $c['name'];
            $v = $valorDe($c);
            $largo = in_array($c['type'], ['textarea', 'file', 'checkbox'], true) ? 'largo' : '';
            $obrig = !empty($c['required']) && !($c['type'] === 'password' && $registro);
        ?>
            <div class="campo <?= $largo ?> <?= $c['type'] === 'checkbox' ? 'campo-check' : '' ?>">
            <?php if ($c['type'] === 'checkbox'): ?>
                <input type="checkbox" id="<?= e($n) ?>" name="<?= e($n) ?>" value="1" <?= $v ? 'checked' : '' ?>>
                <label for="<?= e($n) ?>"><?= e($c['label']) ?></label>

            <?php else: ?>
                <label for="<?= e($n) ?>"><?= e($c['label']) ?><?= $obrig ? ' *' : '' ?></label>

                <?php if ($c['type'] === 'textarea'): ?>
                    <textarea id="<?= e($n) ?>" name="<?= e($n) ?>" data-rotulo="<?= e($c['label']) ?>"><?= e($v) ?></textarea>

                <?php elseif ($c['type'] === 'select'): ?>
                    <select id="<?= e($n) ?>" name="<?= e($n) ?>" <?= $obrig ? 'required' : '' ?> data-rotulo="<?= e($c['label']) ?>">
                        <option value=""><?= e($c['vazio'] ?? 'Selecione...') ?></option>
                        <?php foreach ($c['options'] as $ov => $ol): ?>
                            <option value="<?= e($ov) ?>" <?= (string)$v === (string)$ov ? 'selected' : '' ?>><?= e($ol) ?></option>
                        <?php endforeach; ?>
                    </select>

                <?php elseif ($c['type'] === 'file'): ?>
                    <?php if ($registro && !empty($registro[$n])): ?>
                        <img class="preview-img" src="<?= e(imagem_produto($registro[$n])) ?>" alt="Imagem atual">
                        <div class="campo-check" style="margin-bottom:.6rem">
                            <input type="checkbox" id="remover_<?= e($n) ?>" name="remover_<?= e($n) ?>" value="1">
                            <label for="remover_<?= e($n) ?>">Remover imagem atual</label>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="<?= e($n) ?>" name="<?= e($n) ?>" accept="image/jpeg,image/png,image/webp,image/gif">

                <?php elseif ($c['type'] === 'password'): ?>
                    <input type="password" id="<?= e($n) ?>" name="<?= e($n) ?>" autocomplete="new-password"
                           <?= $obrig ? 'required' : '' ?> data-min="6" data-rotulo="<?= e($c['label']) ?>">

                <?php else: ?>
                    <input type="<?= e($c['type']) ?>" id="<?= e($n) ?>" name="<?= e($n) ?>" value="<?= e($v) ?>"
                           <?= $obrig ? 'required' : '' ?>
                           <?= isset($c['max']) ? 'maxlength="' . (int)$c['max'] . '"' : '' ?>
                           <?= isset($c['step']) ? 'step="' . e($c['step']) . '"' : '' ?>
                           <?= isset($c['min']) ? 'min="' . e($c['min']) . '"' : '' ?>
                           <?= isset($c['placeholder']) ? 'placeholder="' . e($c['placeholder']) . '"' : '' ?>
                           data-rotulo="<?= e($c['label']) ?>">
                <?php endif; ?>

                <?php if (!empty($c['help'])): ?><div class="ajuda"><?= e($c['help']) ?></div><?php endif; ?>
            <?php endif; ?>
            </div>
        <?php endforeach; ?>
        </div>

        <div style="display:flex;gap:.6rem;margin-top:.8rem">
            <button class="btn" type="submit"><?= $registro ? 'Salvar alterações' : 'Cadastrar' ?></button>
            <a class="btn btn-outline" href="<?= url("admin/$recurso") ?>">Cancelar</a>
        </div>
    </form>
</div>
