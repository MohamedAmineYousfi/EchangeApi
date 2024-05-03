@extends('emails.layout')

@section('content')
<table class="email-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
  <tr>
    <td align="center">
      <table class="email-content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td class="email-masthead">
            <a href="{{config('app.front_app_url')}}" class="f-fallback email-masthead_name">
              {{ config('app.name') }}
            </a>
          </td>
        </tr>
        <!-- Email Body -->
        <tr>
          <td class="email-body" width="570" cellpadding="0" cellspacing="0">
            <table class="email-body_inner" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
              <!-- Body content -->
              <tr>
                <td class="content-cell">
                  <div class="f-fallback">
                    <h1>Bonjour {{$payment->invoice->billing_firstname}},</h1>
                    <p>Votre paiement {{$payment->code}} de ${{$payment->amount}} pour la facture {{$payment->invoice->code}} a bien été reçu.</p>

                    <table class="attributes" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                      <tr>
                        <td class="attributes_content">
                          <table width="100%" cellpadding="0" cellspacing="0" role="presentation">
                            <tr>
                              <td class="attributes_item">
                                <span class="f-fallback">
                                  <strong>Montant :</strong> ${{$payment->amount}}
                                </span>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>

                    <p>En cas d'erreur ou pour avoir plus d'informations, veuillez contacter notre <a href="mailto:info@chirurgiequebec.ca">équipe d'assistance</a> pour obtenir de l'aide.</p>
                    <p>Coordialement,
                      <br>L'equipe {{ config('app.name') }}.
                    </p>
                  </div>
                </td>
              </tr>
            </table>
          </td>
        </tr>
        <tr>
          <td>
            <table class="email-footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
                <td class="content-cell" align="center">
                  <p class="f-fallback sub align-center">
                    {{ config('app.name') }}
                  </p>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
@endsection