<?php
/**
 * Controller base: renderização de views e verificação de acesso.
 */
abstract class Controller
{
    /**
     * Renderiza uma view dentro de um layout.
     * @param string $view   caminho em app/views (sem .php)
     * @param array  $dados  variáveis disponíveis na view
     * @param string $layout 'loja', 'admin' ou 'vazio'
     */
    protected function view(string $view, array $dados = [], string $layout = 'loja'): void
    {
        extract($dados, EXTR_SKIP);
        ob_start();
        require ROOT_PATH . '/app/views/' . $view . '.php';
        $conteudo = ob_get_clean();
        $titulo = $titulo ?? APP_NAME;
        require ROOT_PATH . '/app/views/layouts/' . $layout . '.php';
        limpar_old();
    }

    protected function ehPost(): bool
    {
        return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
    }

    /** Exige login de usuário (admin/gerente). */
    protected function exigirAdmin(): void
    {
        if (!Auth::ehAdminArea()) {
            flash('erro', 'Faça login como administrador para acessar essa área.');
            redirect('login');
        }
    }

    /** Exige login de cliente. */
    protected function exigirCliente(): void
    {
        if (!Auth::ehCliente()) {
            flash('erro', 'Faça login (ou crie sua conta) para continuar.');
            $_SESSION['voltar_para'] = $_GET['url'] ?? '';
            redirect('login');
        }
    }

    protected function naoEncontrado(): void
    {
        http_response_code(404);
        $this->view('erros/404', ['titulo' => 'Página não encontrada']);
        exit;
    }
}
