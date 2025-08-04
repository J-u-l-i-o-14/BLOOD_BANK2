<?php

namespace App\Exports;

use App\Models\BloodBag;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class BloodBagsExport implements FromCollection, WithHeadings, WithMapping, WithStyles, ShouldAutoSize
{
    protected $bloodBags;

    public function __construct($bloodBags)
    {
        $this->bloodBags = $bloodBags;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return $this->bloodBags;
    }

    /**
     * @return array
     */
    public function headings(): array
    {
        return [
            'ID',
            'Type sanguin',
            'Volume (ml)',
            'Centre de collecte',
            'Statut',
            'Date de prélèvement',
            'Date d\'expiration',
            'Nom du donneur',
            'Téléphone du donneur',
            'Créé le',
            'Mis à jour le'
        ];
    }

    /**
     * @param BloodBag $bloodBag
     * @return array
     */
    public function map($bloodBag): array
    {
        return [
            $bloodBag->id,
            $bloodBag->bloodType->name,
            $bloodBag->quantity_ml,
            $bloodBag->center->name,
            match($bloodBag->status) {
                'available' => 'Disponible',
                'reserved' => 'Réservée',
                'used' => 'Utilisée',
                'expired' => 'Expirée',
                default => 'Inconnu'
            },
            $bloodBag->collection_date->format('d/m/Y'),
            $bloodBag->expiry_date->format('d/m/Y'),
            $bloodBag->donor_name,
            $bloodBag->donor_phone,
            $bloodBag->created_at->format('d/m/Y H:i'),
            $bloodBag->updated_at->format('d/m/Y H:i')
        ];
    }

    /**
     * @param Worksheet $sheet
     */
    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('A1:K1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => ['rgb' => '000000']
            ]
        ]);

        // Style conditionnel pour les poches expirées ou expirant bientôt
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle('G2:G'.$lastRow)->setConditionalStyles([
            new \PhpOffice\PhpSpreadsheet\Style\Conditional([
                'condition' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION,
                    'formula' => ['NOW()>=$G2']
                ],
                'style' => [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFCCCC']
                    ]
                ]
            ]),
            new \PhpOffice\PhpSpreadsheet\Style\Conditional([
                'condition' => [
                    'type' => \PhpOffice\PhpSpreadsheet\Style\Conditional::CONDITION_EXPRESSION,
                    'formula' => ['AND(NOW()<$G2,NOW()+7>=$G2)']
                ],
                'style' => [
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FFEB9C']
                    ]
                ]
            ])
        ]);

        // Bordures pour toutes les cellules
        $sheet->getStyle('A1:K'.$lastRow)->getBorders()->getAllBorders()->setBorderStyle(
            \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN
        );

        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}
