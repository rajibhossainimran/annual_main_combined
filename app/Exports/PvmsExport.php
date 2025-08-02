<?php

namespace App\Exports;

use App\Models\NotesheetDemandPVMS;
use App\Models\PVMS;
use DateTime;
// use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
// use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

// use PhpOffice\PhpSpreadsheet\Shared\Date;
// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
// use Maatwebsite\Excel\Concerns\WithColumnFormatting;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;

class PvmsExport implements ShouldAutoSize, WithMapping,WithHeadings,WithEvents,FromCollection,WithCustomStartCell,WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // sl pvms nomeclature a/u qty unit_price

    private $note_sheets;
    
    public function __construct(array $note_sheets=[])
    {
        $this->note_sheets = $note_sheets;
    }

    public function collection()
    {
       $uniq_pvms = NotesheetDemandPVMS::select('pvms_id', \DB::raw('SUM(total_quantity) as total_quantity'))->whereIn('notesheet_id', $this->note_sheets)->groupBy('pvms_id')->get();
       return $uniq_pvms;
    }

    public function map($data): array
    {
        return [
            $data->PVMS->pvms_id,
            $data->PVMS->nomenclature,
            $data->PVMS->unitName->name,
        ];
    }

    public function headings(): array
    {
        return [
            'PVMS',
            'Nomenclature',
            'A/U',
            'Unit Price',
            'Specification'
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getStyle('A1:I1')->applyFromArray([
                    'font' => [
                        'bold' => true
                    ]
                ]);
            }
        ];
    }


    public function startCell(): string
    {
        return 'A1';
    }

    public function title(): string
    {
        return 'PVMS';
    }
}
