<?php

namespace App\Helpers;

use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as DomPDFPDF;
use Exception;
use Illuminate\Support\Facades\Mail;

class DocumentsMailSender
{
    public static function sendMailWithAttachment($view, $viewData, $messageData = null): bool
    {
        try {
            /** @var DomPDFPDF */
            $pdf = Pdf::loadView(
                $view,
                $viewData
            )->setPaper('a4', 'portrait');

            // Générer le PDF
            $pdf->output();
            $domPdf = $pdf->getDomPDF();
            $canvas = $domPdf->getCanvas();
            $canvas->page_text(15, $canvas->get_height() - 25, __('invoices.page').' {PAGE_NUM} '.__('invoices.of').' {PAGE_COUNT}', 'Arial', 8, [(140 / 255), (143 / 255), (149 / 255)]);

            $tempPath = storage_path('app/public');

            $tempFilename = 'temp_'.time().'.pdf';
            $tempFilePath = $tempPath.'/'.$tempFilename;
            $pdf->save($tempFilePath);
            $filename = $viewData['filename'];

            // Récupérer les données du message
            $to = $messageData['to'] ?? null;
            $cc = $messageData['cc'] ?? null;
            $bcc = $messageData['bcc'] ?? null;
            $subject = $messageData['subject'] ?? 'Sujet de l\'e-mail';
            Mail::send('emails.documents.sales-invoices',
                [
                    'content' => isset($messageData) ? $messageData['body'] ?? null : null,
                    'subject' => isset($messageData) ? $messageData['subject'] ?? null : null,
                    'username' => isset($messageData) ? $messageData['username'] ?? null : null,
                ],
                function ($message) use ($tempFilePath, $filename, $cc, $to, $bcc, $subject) {
                    $message->to($to)
                        ->subject($subject)
                        ->attach($tempFilePath, [
                            'as' => $filename,
                            'mime' => 'application/pdf',
                        ]);
                    if ($cc) {
                        $message->cc($cc);
                    }

                    if ($bcc) {
                        $message->bcc($bcc);
                    }
                });

            unlink($tempFilePath);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}
