<?php

namespace App\Exports;

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

class AnnualDeamndExport implements ShouldAutoSize, WithMapping,WithHeadings,WithEvents,FromCollection,WithCustomStartCell,WithTitle
{
    /**
    * @return \Illuminate\Support\Collection
    */
    // sl pvms nomeclature a/u qty unit_price

    private $annual_demand_unit_ids;

    public function __construct(array $annual_demand_unit_ids=[])
    {
        $this->annual_demand_unit_ids = $annual_demand_unit_ids;
    }

    public function collection()
    {
        $annual_demand_pvms = AnnualDemandPvmsUnitDemand::with('annualDemandPvms.PVMS')
        ->whereHas('annualDemandPvms.PVMS')
        ->whereIn('annual_demand_unit_id', $this->annual_demand_unit_ids)
        ->get()
        ->groupBy(fn($item) => optional($item->annualDemandPvms->PVMS)->id)
        ->map(function ($group) {
            return [
                'pvms_id' => $group->first()->annualDemandPvms->PVMS->pvms_id ?? null,
                'nomenclature' => $group->first()->annualDemandPvms->PVMS->nomenclature ?? null,
                'unitName' => $group->first()->annualDemandPvms->PVMS->unitName->name ?? null,
                'total_qty' => $group->sum('dg_qty'),
            ];
        });

        return $annual_demand_pvms;
    }

    public function map($data): array
    {
        return [
            $data["pvms_id"],
            $data["nomenclature"],
            $data["unitName"],
            $data["total_qty"]
        ];
    }

    public function headings(): array
    {
        return [
            'PVMS',
            'Nomenclature',
            'A/U',
            'Qty'
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
        return 'Annual Demand';
    }
}
