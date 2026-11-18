<?php
/** Relatório de vendas (admin/relatorios) com filtro por período e exportação CSV. */
class RelatorioController extends Controller
{
    public function __construct()
    {
        $this->exigirAdmin();
    }

    public function index(): void
    {
        $de = $this->data($_GET['de'] ?? '', date('Y-m-d', strtotime('-60 days')));
        $ate = $this->data($_GET['ate'] ?? '', date('Y-m-d'));
        $status = (string)($_GET['status'] ?? '');

        $pedidos = new Pedido();
        $vendas = $pedidos->vendasPorPeriodo($de, $ate, $status);

        if (!empty($_GET['exportar'])) {
            $this->csv($vendas, $de, $ate);
            return;
        }

        $validos = array_filter($vendas, fn($v) => $v['status'] !== 'cancelado');
        $this->view('admin/relatorios', [
            'titulo'       => 'Relatório de vendas',
            'de'           => $de,
            'ate'          => $ate,
            'status'       => $status,
            'statusLista'  => Pedido::STATUS,
            'vendas'       => $vendas,
            'faturamento'  => array_sum(array_column($validos, 'total')),
            'qtdValidos'   => count($validos),
            'maisVendidos' => $pedidos->maisVendidos($de, $ate),
            'porCategoria' => $pedidos->receitaPorCategoria($de, $ate),
        ], 'admin');
    }

    private function data(string $valor, string $padrao): string
    {
        $d = DateTime::createFromFormat('Y-m-d', $valor);
        return ($d && $d->format('Y-m-d') === $valor) ? $valor : $padrao;
    }

    private function csv(array $vendas, string $de, string $ate): void
    {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=vendas_{$de}_a_{$ate}.csv");
        $out = fopen('php://output', 'w');
        fwrite($out, "\xEF\xBB\xBF"); // BOM para o Excel reconhecer acentos
        fputcsv($out, ['Pedido', 'Data', 'Cliente', 'Status', 'Total'], ';');
        foreach ($vendas as $v) {
            fputcsv($out, [$v['id'], data_br($v['criado_em'], true), $v['cliente'], $v['status'],
                number_format((float)$v['total'], 2, ',', '')], ';');
        }
        fclose($out);
    }
}
