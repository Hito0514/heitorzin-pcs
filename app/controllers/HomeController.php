<?php
/** Páginas públicas da loja: início, catálogo (busca/filtro) e detalhes do produto. */
class HomeController extends Controller
{
    public function index(): void
    {
        $produtos = new Produto();
        $this->view('loja/home', [
            'titulo'     => APP_NAME . ' - Peças para PC',
            'destaques'  => $produtos->destaques(4),
            'recentes'   => $produtos->recentes(8),
            'categorias' => (new Categoria())->listarComQuantidade(),
        ]);
    }

    public function loja(): void
    {
        $busca = trim($_GET['q'] ?? '');
        $cat = (int)($_GET['categoria'] ?? 0);
        $ordem = $_GET['ordem'] ?? 'recentes';

        $this->view('loja/catalogo', [
            'titulo'     => 'Catálogo',
            'produtos'   => (new Produto())->catalogo($busca, $cat, $ordem),
            'categorias' => (new Categoria())->listarComQuantidade(),
            'busca'      => $busca,
            'cat'        => $cat,
            'ordem'      => $ordem,
        ]);
    }

    public function produto(int $id): void
    {
        $model = new Produto();
        $p = $model->buscarCompleto($id);
        if (!$p || !$p['ativo']) {
            $this->naoEncontrado();
        }
        $relacionados = array_filter(
            $model->catalogo('', (int)$p['categoria_id']),
            fn($r) => (int)$r['id'] !== $id
        );
        $this->view('loja/produto', [
            'titulo'       => $p['nome'],
            'p'            => $p,
            'relacionados' => array_slice($relacionados, 0, 4),
        ]);
    }
}
