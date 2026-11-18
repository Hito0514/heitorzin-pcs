<?php
/**
 * Configurações gerais da aplicação Heitorzin PCs.
 * Altere apenas os dados do banco se o seu MySQL tiver senha.
 */

// ---- Banco de dados (XAMPP padrão: usuário root, sem senha) ----
define('DB_HOST', '127.0.0.1');
define('DB_PORT', '3306');
define('DB_NAME', 'heitorzin_pcs');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ---- Aplicação ----
define('APP_NAME', 'Heitorzin PCs');
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/public/uploads');
define('UPLOAD_MAX_BYTES', 2 * 1024 * 1024); // 2 MB

// URL base detectada automaticamente (funciona no XAMPP e no servidor embutido do PHP)
$__dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
define('BASE_URL', rtrim($__dir, '/') . '/');
unset($__dir);
