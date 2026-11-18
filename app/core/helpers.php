<?php
/**
 * Funções auxiliares usadas em controllers e views.
 */

/** Escapa texto para HTML (proteção contra XSS). */
function e($valor): string
{
    return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8');
}

/** Gera uma URL da aplicação: url('admin/produtos', ['page' => 2]) */
function url(string $rota = '', array $query = []): string
{
    $rota = trim($rota, '/');
    if ($rota === '' && !$query) {
        return BASE_URL;
    }
    $params = $rota !== '' ? ['url' => $rota] : [];
    return BASE_URL . 'index.php?' . http_build_query($params + $query);
}

/** URL de arquivos estáticos (css, js, img). */
function asset(string $caminho): string
{
    return BASE_URL . 'assets/' . ltrim($caminho, '/');
}

/** URL da imagem de um produto (ou imagem padrão). */
function imagem_produto(?string $arquivo): string
{
    if ($arquivo && is_file(UPLOAD_PATH . '/' . $arquivo)) {
        return BASE_URL . 'uploads/' . rawurlencode($arquivo);
    }
    return asset('img/placeholder.svg');
}

function redirect(string $rota, array $query = []): void
{
    header('Location: ' . url($rota, $query));
    exit;
}

function dinheiro($valor): string
{
    return 'R$ ' . number_format((float)$valor, 2, ',', '.');
}

function data_br(?string $data, bool $hora = false): string
{
    if (!$data) {
        return '-';
    }
    return date($hora ? 'd/m/Y H:i' : 'd/m/Y', strtotime($data));
}

// ---------- Mensagens flash ----------
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'msg' => $mensagem];
}

function pegar_flash(): array
{
    $m = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $m;
}

// ---------- Valores antigos de formulário ----------
function old(string $campo, $padrao = '')
{
    return $_SESSION['old'][$campo] ?? $padrao;
}

function guardar_old(array $dados): void
{
    unset($dados['senha'], $dados['senha2'], $dados['csrf']);
    $_SESSION['old'] = $dados;
}

function limpar_old(): void
{
    unset($_SESSION['old']);
}

// ---------- CSRF ----------
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

function csrf_valido(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado) && hash_equals(csrf_token(), $enviado);
}

/** Badge de status do pedido. */
function badge_status(string $status): string
{
    return '<span class="badge badge-' . e($status) . '">' . e(ucfirst($status)) . '</span>';
}
