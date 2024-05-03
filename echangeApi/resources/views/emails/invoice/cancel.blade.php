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
                    <h1>Bonjour {{$invoice->billing_firstname}},</h1>
                    <p>Votre facture {{$invoice->code}} a été annulée. En cas d'erreur ou pour avoir plus d'informations, veuillez contacter notre <a href="mailto:info@chirurgiequebec.ca">équipe d'assistance</a> pour obtenir de l'aide.</p>
                    <table class="purchase" width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td>
                          <h3>{{$invoice->code}}</h3>
                        </td>
                        <td>
                          <h3 class="align-right">{{$invoice->created_at}}</h3>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          <table class="purchase_content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <th class="purchase_heading" align="left">
                                <p class="f-fallback">Description</p>
                              </th>
                              <th class="purchase_heading" align="right">
                                <p class="f-fallback">Montant</p>
                              </th>
                            </tr>
                            @foreach ($invoice->items as $item)
                            <tr>
                              <td width="70%" class="purchase_item">
                                <span class="f-fallback">{{$item->code}}</span> <br>
                                <span class="f-fallback item_excerpt">{{$item->excerpt}}</span>
                              </td>
                              <td class="align-right" width="30%" class="purchase_item">
                                <span class="f-fallback">{{$item->getItemSubTotalAmount()}}</span>
                              </td>
                            </tr>
                            @endforeach
                            <tr>
                              <td width="70%" class="purchase_footer" valign="bottom">
                                <p class="f-fallback purchase_total purchase_total--label">Sous Total</p>
                              </td>
                              <td width="30%" class="purchase_footer" valign="bottom">
                                <p class="f-fallback purchase_total">${{$invoice->getInvoiceSubTotalAmount()}}</p>
                              </td>
                            </tr>
                            <tr>
                              <td width="70%" class="purchase_footer" valign="bottom">
                                <p class="f-fallback purchase_total purchase_total--label">Taxes</p>
                              </td>
                              <td width="30%" class="purchase_footer" valign="bottom">
                                @foreach ($invoice->getInvoiceTaxes()['details'] as $tax)
                                <p class="f-fallback purchase_tax">
                                  {{$tax['name']}} : ${{$tax['amount']}}
                                </p>
                                @endforeach
                                <p class="f-fallback purchase_total">
                                  Total: ${{$invoice->getInvoiceTaxes()['total']}}
                                </p>
                              </td>
                            </tr>
                            <tr>
                              <td width="70%" class="purchase_footer" valign="bottom">
                                <p class="f-fallback purchase_total purchase_total--label">Total</p>
                              </td>
                              <td width="30%" class="purchase_footer" valign="bottom">
                                <p class="f-fallback purchase_total">${{$invoice->getInvoiceTotalAmount()}}</p>
                              </td>
                            </tr>
                          </table>
                        </td>
                      </tr>
                    </table>
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