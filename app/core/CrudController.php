<?php
/**
 * Controller genérico de CRUD para a área administrativa.
 * Cada controller filho só descreve: recurso, colunas da listagem e campos do formulário.
 *
 * Ações (URLs):
 *   admin/{recurso}                 index      (Read - listagem)
 *   admin/{recurso}/criar           criar      (Create - formulário)
 *   admin/{recurso}/salvar          salvar     (Create - POST)
 *   admin/{recurso}/editar/{id}     editar     (Update - formulário)
 *   admin/{recurso}/atualizar/{id}  atualizar  (Update - POST)
 *   admin/{recurso}/excluir/{id}    excluir    (Delete - POST)
 */
abstract class CrudController extends Controller
{
    protected string $recurso;   // ex.: 'produtos'  (parte da URL)
    protected string $titulo;    // ex.: 'Produtos'
    protected string $singular;  // ex.: 'Produto'
    protected Model $model;

    /** Colunas da tabela: [['label'=>'Nome','key'=>'nome','fmt'=>'money|date|bool|image|callable'], ...] */
    abstract protected function colunas(): array;

    /** Campos do formulário: [['name','label','type','required','max','options','step','min','nullable','help']] */
    abstract protected function campos(): array;

    public function __construct()
    {
        $this->exigirAdmin();
    }

    // ------------------------------------------------------------ Hooks
    /** Registros exibidos na listagem. */
    protected function registros(): array
    {
        return $this->model->listar();
    }

    /** Ajustes nos dados antes de gravar. */
    protected function preparar(array $dados, ?array $atual): array
    {
        return $dados;
    }

    /** Retorna mensagem de erro se o registro não puder ser excluído. */
    protected function bloqueioExclusao(array $registro): ?string
    {
        return null;
    }

    protected function aposExcluir(array $registro): void
    {
    }

    // ------------------------------------------------------------ Ações
    public function index(): void
    {
        $this->view('admin/crud/index', [
            'titulo'   => $this->titulo,
            'recurso'  => $this->recurso,
            'singular' => $this->singular,
            'colunas'  => $this->colunas(),
            'registros' => $this->registros(),
        ], 'admin');
    }

    public function criar(): void
    {
        $this->formulario(null);
    }

    public function salvar(): void
    {
        $this->gravar(null);
    }

    public function editar(int $id): void
    {
        $this->formulario($this->obter($id));
    }

    public function atualizar(int $id): void
    {
        $this->obter($id);
        $this->gravar($id);
    }

    public function excluir(int $id): void
    {
        $registro = $this->obter($id);
        if (!$this->ehPost()) {
            redirect("admin/{$this->recurso}");
        }
        if ($erro = $this->bloqueioExclusao($registro)) {
            flash('erro', $erro);
            redirect("admin/{$this->recurso}");
        }
        try {
            $this->model->excluir($id);
            $this->aposExcluir($registro);
            flash('sucesso', "{$this->singular} excluído(a) com sucesso.");
        } catch (PDOException $e) {
            flash('erro', "Não é possível excluir: este(a) {$this->singular} está vinculado(a) a outros registros.");
        }
        redirect("admin/{$this->recurso}");
    }

    // ------------------------------------------------------------ Internos
    protected function obter(int $id): array
    {
        $r = $this->model->buscar($id);
        if (!$r) {
            $this->naoEncontrado();
        }
        return $r;
    }

    protected function formulario(?array $registro): void
    {
        $this->view('admin/crud/form', [
            'titulo'   => ($registro ? 'Editar ' : 'Novo(a) ') . $this->singular,
            'recurso'  => $this->recurso,
            'singular' => $this->singular,
            'campos'   => $this->campos(),
            'registro' => $registro,
        ], 'admin');
    }

    /** Valida, trata uploads/senhas e grava (INSERT quando $id = null, senão UPDATE). */
    protected function gravar(?int $id): void
    {
        if (!$this->ehPost()) {
            redirect("admin/{$this->recurso}");
        }
        $atual = $id ? $this->model->buscar($id) : null;
        $voltar = $id ? "admin/{$this->recurso}/editar/$id" : "admin/{$this->recurso}/criar";

        [$dados, $erros] = $this->coletar($id === null);
        if ($erros) {
            foreach ($erros as $erro) {
                flash('erro', $erro);
            }
            guardar_old($_POST);
            redirect($voltar);
        }

        // Uploads de imagem
        $novosArquivos = [];
        $antigos = [];
        try {
            foreach ($this->campos() as $c) {
                if ($c['type'] !== 'file') {
                    continue;
                }
                $nome = $c['name'];
                $enviado = Upload::imagem($_FILES[$nome] ?? null);
                if ($enviado) {
                    $dados[$nome] = $enviado;
                    $novosArquivos[] = $enviado;
                    $antigos[] = $atual[$nome] ?? null;
                } elseif ($atual && !empty($_POST["remover_$nome"])) {
                    $dados[$nome] = null;
                    $antigos[] = $atual[$nome] ?? null;
                }
            }
        } catch (RuntimeException $e) {
            foreach ($novosArquivos as $a) {
                Upload::remover($a);
            }
            flash('erro', $e->getMessage());
            guardar_old($_POST);
            redirect($voltar);
        }

        $dados = $this->preparar($dados, $atual);

        try {
            if ($id === null) {
                $this->model->criar($dados);
                flash('sucesso', "{$this->singular} cadastrado(a) com sucesso!");
            } else {
                $this->model->atualizar($id, $dados);
                flash('sucesso', "{$this->singular} atualizado(a) com sucesso!");
            }
        } catch (PDOException $e) {
            foreach ($novosArquivos as $a) {
                Upload::remover($a);
            }
            $msg = $e->getCode() === '23000'
                ? 'Já existe um registro com esse valor único (e-mail ou nome) ou há uma referência inválida.'
                : 'Erro ao gravar no banco de dados.';
            flash('erro', $msg);
            guardar_old($_POST);
            redirect($voltar);
        }

        foreach ($antigos as $a) {
            Upload::remover($a);
        }
        redirect("admin/{$this->recurso}");
    }

    /**
     * Lê o $_POST conforme a definição dos campos e valida.
     * @return array [dados, erros]
     */
    protected function coletar(bool $criando): array
    {
        $dados = [];
        $erros = [];
        foreach ($this->campos() as $c) {
            $nome  = $c['name'];
            $tipo  = $c['type'];
            $label = $c['label'];

            if ($tipo === 'file') {
                continue;
            }
            if ($tipo === 'checkbox') {
                $dados[$nome] = isset($_POST[$nome]) ? 1 : 0;
                continue;
            }

            $valor = $_POST[$nome] ?? '';
            $valor = is_string($valor) ? trim($valor) : '';

            if ($tipo === 'password') {
                if ($valor === '') {
                    if ($criando && !empty($c['required'])) {
                        $erros[] = "O campo $label é obrigatório.";
                    }
                    continue; // na edição, vazio = manter a senha atual
                }
                if (strlen($valor) < 6) {
                    $erros[] = "$label deve ter pelo menos 6 caracteres.";
                    continue;
                }
                $dados[$nome] = password_hash($valor, PASSWORD_DEFAULT);
                continue;
            }

            if ($valor === '') {
                if (!empty($c['required'])) {
                    $erros[] = "O campo $label é obrigatório.";
                    continue;
                }
                $dados[$nome] = !empty($c['nullable']) ? null : '';
                continue;
            }

            if (!empty($c['max']) && mb_strlen($valor) > $c['max']) {
                $erros[] = "$label deve ter no máximo {$c['max']} caracteres.";
            }
            if ($tipo === 'email' && !filter_var($valor, FILTER_VALIDATE_EMAIL)) {
                $erros[] = 'Informe um e-mail válido.';
            }
            if ($tipo === 'number') {
                $valor = str_replace(',', '.', $valor);
                if (!is_numeric($valor)) {
                    $erros[] = "$label deve ser um número.";
                } elseif (isset($c['min']) && (float)$valor < $c['min']) {
                    $erros[] = "$label deve ser no mínimo {$c['min']}.";
                }
            }
            if ($tipo === 'select' && isset($c['options']) && !array_key_exists($valor, $c['options'])) {
                $erros[] = "Opção inválida em $label.";
            }
            $dados[$nome] = $valor;
        }
        return [$dados, $erros];
    }
}
