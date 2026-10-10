<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CashMovement;
use App\Models\Node;
use App\Services\ExportService;
use Illuminate\Http\Request;

class CashMovementWebController extends Controller
{
    public function __construct(private ExportService $export) {}

    public function index(Request $request)
    {
        $query = $this->buildQuery($request);

        $movements = $query->paginate(25);
        $nodes = Node::orderBy('name')->get();

        return view('admin.cash.index', compact('movements', 'nodes'));
    }

    /**
     * GET /admin/cash-movements/export?format=csv|pdf
     */
    public function export(Request $request)
    {
        $query = $this->buildQuery($request);
        $movements = $query->limit(5000)->get();

        $headers = ['Fecha', 'Nodo', 'Tipo', 'Monto', 'Efectivo Antes', 'Efectivo Después'];
        $rows = $movements->map(fn($m) => [
            $m->created_at->format('d/m/Y H:i'),
            $m->node->name ?? '—',
            strtoupper($m->type),
            '$' . number_format($m->amount, 2),
            '$' . number_format($m->previous_cash, 2),
            '$' . number_format($m->new_cash, 2),
        ])->toArray();

        $format = $request->input('format', 'csv');

        if ($format === 'pdf') {
            return $this->export->pdf(
                'admin.pdf.report',
                [
                    'title'    => 'Reporte de Movimientos de Efectivo',
                    'subtitle' => 'Total: ' . $movements->count() . ' movimientos',
                    'headers'  => $headers,
                    'rows'     => $rows,
                ],
                'reporte_efectivo',
                'landscape'
            );
        }

        return $this->export->csv('reporte_efectivo', $headers, $rows);
    }

    private function buildQuery(Request $request)
    {
        $query = CashMovement::with('node')->orderByDesc('created_at');

        if ($request->filled('node_id')) {
            $query->where('node_id', $request->node_id);
        }
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        return $query;
    }
}