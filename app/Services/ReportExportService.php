<?php

namespace App\Services;

use App\Models\BloodBag;
use Illuminate\Support\Collection;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use Dompdf\Dompdf;
use Carbon\Carbon;

class ReportExportService
{
    protected $data;
    protected $options;
    protected $startDate;
    protected $endDate;

    public function __construct(Collection $data, $startDate, $endDate, array $options = [])
    {
        $this->data = $data;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->options = $options;
    }

    /**
     * Exporte le rapport en Excel
     */
    public function toExcel(): string
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // En-tête
        $sheet->setCellValue('A1', 'Rapport d\'inventaire');
        $sheet->setCellValue('A2', 'Période : ' . $this->startDate->format('d/m/Y') . ' au ' . $this->endDate->format('d/m/Y'));

        // En-têtes des colonnes
        $headers = ['Type sanguin', 'Disponibles', 'Réservées', 'Utilisées', 'Expirées', 'Total'];
        foreach ($headers as $i => $header) {
            $sheet->setCellValueByColumnAndRow($i + 1, 4, $header);
        }

        // Données
        $row = 5;
        foreach ($this->data as $item) {
            $total = $item->available + $item->reserved + $item->used + $item->expired;
            
            $sheet->setCellValueByColumnAndRow(1, $row, $item->name);
            $sheet->setCellValueByColumnAndRow(2, $row, $item->available);
            $sheet->setCellValueByColumnAndRow(3, $row, $item->reserved);
            $sheet->setCellValueByColumnAndRow(4, $row, $item->used);
            $sheet->setCellValueByColumnAndRow(5, $row, $item->expired);
            $sheet->setCellValueByColumnAndRow(6, $row, $total);
            
            $row++;
        }

        // Style du titre
        $sheet->mergeCells('A1:F1');
        $sheet->mergeCells('A2:F2');
        $sheet->getStyle('A1:F2')->getFont()->setBold(true);
        $sheet->getStyle('A1')->getFont()->setSize(16);

        // Style des en-têtes
        $sheet->getStyle('A4:F4')->applyFromArray([
            'font' => ['bold' => true],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'color' => ['rgb' => '4F46E5']
            ],
            'font' => [
                'color' => ['rgb' => 'FFFFFF']
            ]
        ]);

        // Bordures
        $sheet->getStyle('A4:F' . ($row - 1))->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN
                ]
            ]
        ]);

        // Largeur des colonnes
        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Ajouter un graphique si demandé
        if ($this->options['include_charts'] ?? false) {
            $this->addChart($spreadsheet, $row);
        }

        // Créer le fichier
        $writer = new Xlsx($spreadsheet);
        $filename = storage_path('app/temp/report-' . uniqid() . '.xlsx');
        $writer->save($filename);

        return $filename;
    }

    /**
     * Exporte le rapport en PDF
     */
    public function toPdf(): string
    {
        $dompdf = new Dompdf();
        
        // Générer le HTML
        $html = view('exports.report-pdf', [
            'data' => $this->data,
            'startDate' => $this->startDate,
            'endDate' => $this->endDate,
            'options' => $this->options
        ])->render();

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4');
        $dompdf->render();

        // Sauvegarder le PDF
        $filename = storage_path('app/temp/report-' . uniqid() . '.pdf');
        file_put_contents($filename, $dompdf->output());

        return $filename;
    }

    /**
     * Ajoute un graphique au rapport Excel
     */
    protected function addChart(Spreadsheet $spreadsheet, int $startRow): void
    {
        $worksheet = $spreadsheet->getActiveSheet();
        
        $dataSeriesLabels = [
            new DataSeriesValues('String', 'Worksheet!$B$4', null, 1),
            new DataSeriesValues('String', 'Worksheet!$C$4', null, 1),
            new DataSeriesValues('String', 'Worksheet!$D$4', null, 1),
            new DataSeriesValues('String', 'Worksheet!$E$4', null, 1),
        ];
        
        $xAxisTickValues = [
            new DataSeriesValues('String', 'Worksheet!$A$5:$A$' . ($startRow - 1), null, $this->data->count()),
        ];
        
        $dataSeriesValues = [
            new DataSeriesValues('Number', 'Worksheet!$B$5:$B$' . ($startRow - 1), null, $this->data->count()),
            new DataSeriesValues('Number', 'Worksheet!$C$5:$C$' . ($startRow - 1), null, $this->data->count()),
            new DataSeriesValues('Number', 'Worksheet!$D$5:$D$' . ($startRow - 1), null, $this->data->count()),
            new DataSeriesValues('Number', 'Worksheet!$E$5:$E$' . ($startRow - 1), null, $this->data->count()),
        ];
        
        $series = new DataSeries(
            DataSeries::TYPE_BARCHART,
            DataSeries::GROUPING_STANDARD,
            range(0, count($dataSeriesValues) - 1),
            $dataSeriesLabels,
            $xAxisTickValues,
            $dataSeriesValues
        );
        
        $plotArea = new PlotArea(null, [$series]);
        $legend = new Legend(Legend::POSITION_RIGHT, null, false);
        $title = new Title('Répartition par type sanguin');
        
        $chart = new Chart(
            'chart1',
            $title,
            $legend,
            $plotArea
        );
        
        $chart->setTopLeftPosition('A' . ($startRow + 2));
        $chart->setBottomRightPosition('F' . ($startRow + 15));
        
        $worksheet->addChart($chart);
    }
}
