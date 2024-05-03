<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;

class PropertiesExport implements FromCollection, ShouldAutoSize, WithCustomStartCell, WithHeadings
{
    protected $properties;

    public function __construct($properties)
    {
        $this->properties = $properties;
    }

    public function collection(): Collection
    {
        return $this->properties->map(function ($item) {
            return [
                'property_number' => $item->property_number,
                'owners' => $item->getPropertyOwners(),
                'address' =>  $item->address . ', ' . $item->city . ' ' . $item->state . ', ' . $item->zipcode,
                'locations' => $item->getPropertyLocations(),
                'batch_numbers' => $item->getPropertyBatches(),
                'designation' => $item->designation,
                'owed_taxes_municipality' => $item->owed_taxes_municipality,
                'owed_taxes_school_board' => $item->owed_taxes_school_board,
                'mrc_fees' => $item->mrc_fees,
                'total' => $item->getTotal(),
                /*'taxable' => $item->taxable === true ? __('contents.yes', []) : __('contents.no', []),
                'status' => __('contents.'.$item->status, []),*/
            ];
        });
    }

    public function headings(): array
    {
        return [
            __('contents.property_number', []),
            __('contents.owners', []),
            __('contents.address', []),
            __('contents.locations', []),
            __('contents.batch_numbers', []),
            __('contents.designation', []),
            __('contents.owed_taxes_municipality', []),
            __('contents.owed_taxes_school_board', []),
            __('contents.mrc_fees', []),
            __('contents.total', []),
            /*
            __('contents.taxable', []),
            __('contents.status', []),
            */
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->getStyle('1')->getFont()->setBold(true);

                $event->sheet->getColumnDimension('A')->setWidth(200);
                $event->sheet->getColumnDimension('B')->setWidth(200);

                $event->sheet->getRowDimension(1)->setRowHeight(50);
            },
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }
}
