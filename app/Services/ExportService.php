<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportService
{
    /**
     * Genera una respuesta HTTP con un archivo CSV descargable.
     *
     * @param string $filename  Nombre del archivo (sin extensión)
     * @param array  $headers   Encabezados de las columnas
     * @param array  $rows      Filas de datos (array de arrays)
     */
    public function csv(string $filename, array $headers, array $rows): StreamedResponse
    {
        $filename = $filename . '_' . now()->format('Y-m-d_His') . '.csv';

        return response()->streamDownload(function () use ($headers, $rows) {
            $handle = fopen('php://output', 'w');

            // BOM UTF-8 para que Excel abra bien los acentos
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, $headers);

            foreach ($rows as $row) {
                fputcsv($handle, $row);
            }

            fclose($handle);
        }, $filename, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Genera un PDF descargable desde una vista Blade.
     *
     * @param string $view       Nombre de la vista Blade
     * @param array  $data       Datos para la vista
     * @param string $filename   Nombre del archivo (sin extensión)
     * @param string $orientation 'portrait' o 'landscape'
     */
    public function pdf(string $view, array $data, string $filename, string $orientation = 'portrait')
    {
        $data['generated_at'] = now();
        $data['generated_by'] = session('admin.full_name', 'Admin');

        $pdf = Pdf::loadView($view, $data)
            ->setPaper('letter', $orientation)
            ->setOptions([
                'defaultFont'   => 'DejaVu Sans',
                'isRemoteEnabled' => true,
            ]);

        $filename = $filename . '_' . now()->format('Y-m-d_His') . '.pdf';

        return $pdf->download($filename);
    }
}