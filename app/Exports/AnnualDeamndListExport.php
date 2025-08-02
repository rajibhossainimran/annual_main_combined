<?php

namespace App\Exports;

use App\Models\AnnualDemandPvms;
use App\Models\AnnualDemandPvmsUnitDemand;
use App\Models\NotesheetDemandPVMS;
use App\Models\PVMS;
use DateTime;
use Illuminate\Support\Facades\DB;
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

class AnnualDeamndListExport implements ShouldAutoSize, WithMapping,WithHeadings,WithEvents,FromCollection,WithCustomStartCell,WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // sl pvms nomeclature a/u qty unit_price

    private $annual_demand_id;

    public function __construct($annual_demand_id)
    {
        $this->annual_demand_id = $annual_demand_id;
    }

    public function collection()
    {
        $annual_demand_id = $this->annual_demand_id;
        $annual_demand_pvms = AnnualDemandPvms::with(['PVMS' => function ($query) {
            $query->orderBy('nomenclature', 'asc');
        },'PVMS.unitName','annualDemandDepartment' ])->whereHas('annualDemandDepartment' , function ($query) use($annual_demand_id) {
            $query->where('annual_demand_id', $annual_demand_id);
        })->groupBy('pvms_id')->get();

        return $annual_demand_pvms;
    }

    public function map($data): array
    {
        return [
            $data->PVMS->pvms_id,
            $data->PVMS->nomenclature,
            $data->PVMS->unitName->name,
            $data->PVMS->itemTypename->name
        ];
    }

    public function headings(): array
    {
        return [
            'PVMS',
            'Nomenclature',
            'A/U',
            'Item Type'
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
        return 'Annual Demand List';
    }
}
