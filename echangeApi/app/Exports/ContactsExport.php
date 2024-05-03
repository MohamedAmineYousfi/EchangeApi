<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;

class ContactsExport implements FromCollection, WithColumnWidths, WithCustomStartCell, WithHeadings, WithMapping, WithStyles
{
    protected $contacts;

    public function __construct($contacts)
    {
        $this->contacts = $contacts;
    }

    public function collection(): Collection
    {
        return $this->contacts;
    }

    public function map($contact): array
    {
        return [
            $contact->title,
            $contact->firstname,
            $contact->lastname,
            $contact->company_name,
            $contact->email,
            $contact->phone,
            $contact->country,
            $contact->state,
            $contact->city,
            $contact->zipcode,
            $contact->address,
        ];
    }

    public function headings(): array
    {
        return [
            __('contents.title', []),
            __('contents.firstname', []),
            __('contents.lastname', []),
            __('contents.company_name', []),
            __('contents.email', []),
            __('contents.phone', []),
            __('contents.country', []),
            __('contents.state', []),
            __('contents.city', []),
            __('contents.zipcode', []),
            __('contents.address', []),
        ];
    }

    public function startCell(): string
    {
        return 'A1';
    }

    public function columnWidths(): array
    {
        return [
            'B' => 20,
            'C' => 20,
            'D' => 30,
            'E' => 30,
            'F' => 25,
            'G' => 20,
            'H' => 20,
            'I' => 20,
            'J' => 50,
        ];
    }

    public function styles($sheet)
    {
        $sheet->getStyle('A1:'.$sheet->getHighestColumn().'1')->applyFromArray([
            'font' => [
                'bold' => true,
            ],
        ]);
    }
}
