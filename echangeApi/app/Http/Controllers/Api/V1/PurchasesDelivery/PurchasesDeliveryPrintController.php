<?php

namespace App\Http\Controllers\Api\V1\PurchasesDelivery;

use App\Helpers\DocumentsMailSender;
use App\Http\Requests\Api\V1\Notification\MailSendFormRequest;
use App\Http\Requests\Api\V1\PurchasesDelivery\PurchaseDeliveryPrintRequest;
use App\Models\PurchasesDelivery;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use CloudCreativity\LaravelJsonApi\Document\Error\Error;
use CloudCreativity\LaravelJsonApi\Http\Controllers\JsonApiController;
use Illuminate\Support\Str;

class PurchasesDeliveryPrintController extends JsonApiController
{
    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function download(PurchaseDeliveryPrintRequest $request, PurchasesDelivery $order)
    {
        $filename = Str::slug($order->code).'.pdf';
        /** @var DomPDFPDF */
        $pdf = Pdf::loadView(
            'pdf.purchasesDelivery.print',
            [
                'filename' => $filename,
                'order' => $order,
            ]
        )->setPaper('a4', 'portrait');

        $pdf->output();
        $domPdf = $pdf->getDomPDF();
        $canvas = $domPdf->get_canvas();
        $canvas->page_text(15, $canvas->get_height() - 25, __('invoices.page').' {PAGE_NUM} '.__('invoices.of').' {PAGE_COUNT}', 'Arial', 8, [(140 / 255), (143 / 255), (149 / 255)]);

        return $pdf->download($filename);
    }

    /**
     * Handle the incoming request.
     *
     * @return mixed
     */
    public function sendMail(MailSendFormRequest $request, PurchasesDelivery $order)
    {
        $data = $request->all();
        if (isset($data['sendMeCopy']) && $data['sendMeCopy']) {
            if (isset($data['cc'])) {
                array_push($data['cc'], auth()->user()->email);
            } else {
                $data['cc'] = auth()->user()->email;
            }
        }
        $filename = Str::slug($order->code).'.pdf';
        $data['username'] = $order->issuer->company_name;

        $response = DocumentsMailSender::sendMailWithAttachment('pdf.purchasesDelivery.print',
            [
                'filename' => $filename,
                'order' => $order,
            ],
            $data,
        );

        if (! $response) {
            return $this->reply()->errors([
                Error::fromArray([
                    'title' => 'Error',
                    'detail' => 'An error occurred while sending the email. Try Again.',
                    'status' => 500,
                ]),
            ]);
        }

        return response()->json([
            'success' => true,
            'title' => 'Success',
            'detail' => 'The email was sent successfully.',
            'status' => 200,
        ]);
    }
}
