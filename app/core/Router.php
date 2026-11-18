<?php
/**
 * Roteador simples (front controller).
 *
 * Formato das URLs:  index.php?url=controller/acao/parametros
 *   ""                       -> HomeController::index
 *   loja                     -> HomeController::loja
 *   produto/5                -> HomeController::produto(5)
 *   carrinho/adicionar       -> CarrinhoController::adicionar
 *   admin                    -> DashboardController::index
 *   admin/produtos           -> ProdutoController::index
 *   admin/produtos/editar/3  -> ProdutoController::editar(3)
 */
class Router
{
    /** Rotas públicas: primeiro segmento => [Controller, ação padrão] */
    private array $publicas = [
        ''         => ['HomeController', 'index'],
        'loja'     => ['HomeController', 'loja'],
        'produto'  => ['HomeController', 'produto'],
        'carrinho' => ['CarrinhoController', 'index'],
        'login'    => ['AuthController', 'login'],
        'logout'   => ['AuthController', 'logout'],
        'registro' => ['AuthController', 'registro'],
        'conta'    => ['ContaController', 'index'],
    ];

    /** Recursos da área administrativa: segmento => Controller */
    private array $admin = [
        'dashboard'    => 'DashboardController',
        'produtos'     => 'ProdutoController',
        'categorias'   => 'CategoriaController',
        'fornecedores' => 'FornecedorController',
        'clientes'     => 'ClienteController',
        'usuarios'     => 'UsuarioController',
        'pedidos'      => 'PedidoController',
        'relatorios'   => 'RelatorioController',
    ];

    public function despachar(): void
    {
        $url = trim($_GET['url'] ?? '', '/');
        $seg = $url === '' ? [] : explode('/', $url);

        // Proteção CSRF: todo POST precisa do token do formulário
        if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST' && !csrf_valido()) {
            http_response_code(419);
            exit('Sessão expirada ou formulário inválido. Volte e tente novamente.');
        }

        [$classe, $acao, $params] = $this->resolver($seg);

        try {
            if (!$classe || !preg_match('/^[a-z][a-zA-Z0-9]*$/', $acao)) {
                $this->erro404();
            }
            $controller = new $classe();
            if (!method_exists($controller, $acao)) {
                $this->erro404();
            }
            $ref = new ReflectionMethod($controller, $acao);
            if (!$ref->isPublic() || $ref->getNumberOfRequiredParameters() > count($params)) {
                $this->erro404();
            }
            $controller->$acao(...array_slice($params, 0, $ref->getNumberOfParameters()));
        } catch (TypeError $e) {
            $this->erro404(); // parâmetro inválido (ex.: id não numérico)
        } catch (Throwable $e) {
            error_log($e->getMessage() . ' em ' . $e->getFile() . ':' . $e->getLine());
            http_response_code(500);
            exit('<h1>Erro interno</h1><p>Algo deu errado. Tente novamente.</p>');
        }
    }

    private function resolver(array $seg): array
    {
        $primeiro = $seg[0] ?? '';

        if ($primeiro === 'admin') {
            $recurso = $seg[1] ?? 'dashboard';
            $classe = $this->admin[$recurso] ?? null;
            return [$classe, $seg[2] ?? 'index', array_slice($seg, 3)];
        }

        if (!isset($this->publicas[$primeiro])) {
            return [null, '', []];
        }
        [$classe, $padrao] = $this->publicas[$primeiro];

        // /produto/5  -> parâmetros começam no segmento 1
        if ($primeiro === 'produto') {
            return [$classe, $padrao, array_slice($seg, 1)];
        }
        // /carrinho/adicionar, /conta/pedido/3 ...
        if (in_array($primeiro, ['carrinho', 'conta'], true)) {
            return [$classe, $seg[1] ?? 'index', array_slice($seg, 2)];
        }
        return [$classe, $padrao, array_slice($seg, 1)];
    }

    private function erro404(): void
    {
        http_response_code(404);
        $titulo = 'Página não encontrada';
        ob_start();
        require ROOT_PATH . '/app/views/erros/404.php';
        $conteudo = ob_get_clean();
        require ROOT_PATH . '/app/views/layouts/loja.php';
        exit;
    }
}
