<?php
/**
 * Front controller - TODA requisição passa por aqui.
 */
error_reporting(E_ALL);
ini_set('display_errors', '0');
ini_set('log_errors', '1');

require __DIR__ . '/../config/config.php';
require ROOT_PATH . '/app/core/helpers.php';

// Autoload: carrega as classes automaticamente a partir das pastas do MVC
spl_autoload_register(function (string $classe): void {
    foreach (['app/core', 'app/models', 'app/controllers'] as $pasta) {
        $arquivo = ROOT_PATH . "/$pasta/$classe.php";
        if (is_file($arquivo)) {
            require $arquivo;
            return;
        }
    }
});

Auth::iniciarSessao();
(new Router())->despachar();
