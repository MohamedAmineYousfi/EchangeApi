<?php

namespace App\Http\Controllers\Api\V1\Property;

use App\Exports\ContactsExport;
use App\Http\Requests\Api\V1\Property\PropertyPrintRequest;
use App\Models\Property;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Maatwebsite\Excel\Facades\Excel;

class PropertyPrintController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function executeThumbnailsExport($contacts)
    {
        $export = new ContactsExport($contacts);
        $file = Excel::raw($export, \Maatwebsite\Excel\Excel::XLSX);

        return response($file, 200)
            ->header('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet')
            ->header('Content-Disposition', 'attachment; filename="contacts_thumbs_'.now()->format('d/m/Y_H_i_s').'.xlsx"');
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function downloadThumbnails()
    {
        $contacts = Property::with('owners')
            ->whereNotNull('sale_confirmed_at')
            ->orderBy('property_number')
            ->get()
            ->flatMap(function ($property) {
                return $property->owners;
            });

        return $this->executeThumbnailsExport($contacts);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function downloadThumbnailsForSelection(PropertyPrintRequest $request)
    {
        $data = $request->all();
        $selectedProperties = $data['selectedProperties'];

        $contacts = Property::with('owners')
            ->whereIn('id', $selectedProperties)
            ->whereNotNull('sale_confirmed_at')
            ->orderBy('property_number')
            ->get()
            ->flatMap(function ($property) {
                return $property->owners;
            });

        return $this->executeThumbnailsExport($contacts);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function executePdfExport($properties, $details)
    {

        $timezone = $details['timezone'];
        Carbon::setLocale(config('app.locale'));
        $now = Carbon::now();
        $date = $now->translatedFormat('j F Y');
        $formatedDate = $now->format('Y-m-d H:i:s');
        $city = $details['city'];
        $letterDate = Carbon::parse($details['date'])->translatedFormat('j F Y');

        $data = [
            'properties' => $properties,
            'date' => $date,
            'city' => $city,
            'letterDate' => $letterDate,
            'timezone' => $timezone,
        ];
        
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        ini_set('memory_limit', '512M'); // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        // PATCH FOR MEMORY MANAGEMENT TO REPLACE WITH BETTER MEMORY MANAGEMENT
        
        $pdf = PDF::loadView('pdf.property.print', $data)->setPaper('a4', 'portrait');
        $pdf->render();

        return $pdf->stream('Lettre-citoyen-'.$formatedDate.'.pdf');
    }


    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function propertyTransactionPrintInvoice(Property $property)
    {
        /*return view(
        'pdf.property.transaction-print',
            [ 'property' => $property ]
        );*/
        $pdf = PDF::loadView(
            'pdf.property.transaction-print',
            [
                'property' => $property,
            ]
        )->setPaper('a4');

        $pdf->render();
        return $pdf->stream('property-transaction-receipt-'.now()->format('d-m-Y-H-i-s').'pdf');
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function downloadLettersForSelection(PropertyPrintRequest $request)
    {

        $data = $request->all();
        $selectedProperties = $data['selectedProperties'];
        $details = $data['details'];

        $properties = Property::with('owners')
            ->whereIn('id', $selectedProperties)
            ->whereNotNull('sale_confirmed_at')
            ->orderBy('property_number')
            ->get();

        return $this->executePdfExport($properties, $details);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function downloadLetters(PropertyPrintRequest $request)
    {

        $details = $request->all();

        $properties = Property::with('owners')
            ->whereNotNull('sale_confirmed_at')
            ->orderBy('property_number')
            ->get();

        if ($properties->isEmpty()) {
            abort(404);
        } else {
            return $this->executePdfExport($properties, $details);
        }
    }
}
