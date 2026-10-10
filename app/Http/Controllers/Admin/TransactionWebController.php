<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Node;
use App\Models\Transaction;
use App\Services\ExportService;
use Illuminate\Http\Request;

class TransactionWebController extends Controller
{
    public function __construct(private ExportService $export) {}

    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $transactions = $query->paginate(25);
        $nodes = Node::orderBy('name')->get();

        return view('admin.transactions.index', compact('transactions', 'nodes'));
    }

    /**
     * GET /admin/transactions/export?format=csv|pdf
     */
    public function export(Request $request)
    {
        $query = $this->buildQuery($request);
        $transactions = $query->limit(5000)->get();

        $headers = ['Fecha', 'Tipo', 'Monto', 'Nodo', 'Cuenta Origen', 'Cuenta Destino'];
        $rows = $transactions->map(fn($t) => [
            $t->created_at->format('d/m/Y H:i'),
            strtoupper($t->type),
            '$' . number_format($t->amount, 2),
            $t->node->name ?? '—',
            $t->originAccount->account_number ?? '—',
            $t->destinationAccount->account_number ?? '—',
        ])->toArray();

        $format = $request->input('format', 'csv');

        if ($format === 'pdf') {
            return $this->export->pdf(
                'admin.pdf.report',
                [
                    'title'    => 'Reporte de Transacciones',
                    'subtitle' => 'Total: ' . $transactions->count() . ' transacciones',
                    'headers'  => $headers,
                    'rows'     => $rows,
                ],
                'reporte_transacciones',
                'landscape'
            );
        }

        return $this->export->csv('reporte_transacciones', $headers, $rows);
    }

    private function buildQuery(Request $request)
    {
        $query = Transaction::with(['node', 'originAccount', 'destinationAccount'])
            ->orderByDesc('created_at');

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }
        if ($request->filled('node_id')) {
            $query->where('node_id', $request->node_id);
        }

        return $query;
    }
}