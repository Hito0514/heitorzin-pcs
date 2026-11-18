<?php
/** Login, logout e registro de clientes. */
class AuthController extends Controller
{
    public function login(): void
    {
        if (Auth::logado()) {
            redirect(Auth::ehAdminArea() ? 'admin' : '');
        }
        if (!$this->ehPost()) {
            $this->view('auth/login', ['titulo' => 'Entrar']);
            return;
        }

        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo = ($email !== '' && $senha !== '') ? Auth::tentarLogin($email, $senha) : null;

        if (!$tipo) {
            flash('erro', 'E-mail ou senha incorretos.');
            guardar_old(['email' => $email]);
            redirect('login');
        }
        flash('sucesso', 'Bem-vindo, ' . Auth::usuario()['nome'] . '!');
        if ($tipo === 'usuario') {
            redirect('admin');
        }
        $voltar = $_SESSION['voltar_para'] ?? '';
        unset($_SESSION['voltar_para']);
        redirect($voltar);
    }

    public function logout(): void
    {
        if ($this->ehPost()) {
            Auth::logout();
            flash('sucesso', 'Você saiu da sua conta.');
        }
        redirect('');
    }

    public function registro(): void
    {
        if (Auth::logado()) {
            redirect('');
        }
        if (!$this->ehPost()) {
            $this->view('auth/registro', ['titulo' => 'Criar conta']);
            return;
        }

        $nome = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $tel = trim($_POST['telefone'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $senha2 = $_POST['senha2'] ?? '';
        $erros = [];

        if (mb_strlen($nome) < 3 || mb_strlen($nome) > 100) {
            $erros[] = 'Informe seu nome completo (3 a 100 caracteres).';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 120) {
            $erros[] = 'Informe um e-mail válido.';
        }
        $digitos = preg_replace('/\D/', '', $tel);
        if (strlen($digitos) < 10 || strlen($digitos) > 11) {
            $erros[] = 'Informe um telefone válido com DDD.';
        }
        if (strlen($senha) < 6) {
            $erros[] = 'A senha deve ter pelo menos 6 caracteres.';
        }
        if ($senha !== $senha2) {
            $erros[] = 'As senhas não conferem.';
        }
        if (!$erros && ((new Cliente())->buscarPorEmail($email) || (new Usuario())->buscarPorEmail($email))) {
            $erros[] = 'Este e-mail já está cadastrado. Tente fazer login.';
        }

        if ($erros) {
            foreach ($erros as $e) {
                flash('erro', $e);
            }
            guardar_old(['nome' => $nome, 'email' => $email, 'telefone' => $tel]);
            redirect('registro');
        }

        $clientes = new Cliente();
        try {
            $id = $clientes->criar([
                'nome'     => $nome,
                'email'    => $email,
                'telefone' => $tel,
                'senha'    => password_hash($senha, PASSWORD_DEFAULT),
            ]);
        } catch (PDOException $e) {
            flash('erro', 'Não foi possível criar a conta. Tente novamente.');
            guardar_old(['nome' => $nome, 'email' => $email, 'telefone' => $tel]);
            redirect('registro');
        }
        Auth::registrar($clientes->buscar($id), 'cliente');
        flash('sucesso', 'Conta criada com sucesso! Boas compras.');
        $voltar = $_SESSION['voltar_para'] ?? '';
        unset($_SESSION['voltar_para']);
        redirect($voltar);
    }
}
