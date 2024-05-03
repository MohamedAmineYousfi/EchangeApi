@extends('pdf.layout')

@section('document-type')
    {{__('invoices.sale_deliveries')}}
@endsection

@section('document-ref')
    {{__('invoices.invoice')}}
@endsection

@section('bill_to_text')
    {{__('invoices.bill_to_2')}}
@endsection


@section('bill_to')
    <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0;line-height: 9pt;text-align: left;">
        {{$order->recipient->billing_firstname}} {{$order->recipient->billing_lastname}}
    </p>

    <p class="s4" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0;text-align: left;">
        {{$order->recipient->phone}}
    </p>

    <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0;text-align: left;">
        {{$order->recipient->address}}
    </p>

    <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0;text-align: left;">
        <a href="mailto:{{$order->recipient->email}}" class="s4">
            {{$order->recipient->email}}
        </a>
    </p>

@endsection


@section('content')
    <table style="border-collapse:collapse;margin-left:5.5pt; margin-top: 10px; width: 100%" cellspacing="0">
        <tr style="height:25pt;">
            <td style="width:10%" bgcolor="#D0D0D1">
                <p class="s5"
                   style="padding-top: 8pt; padding-bottom: 8pt;padding-left: 6pt;text-indent: 0;text-align: left;">
                    {{__('invoices.date')}}
                </p>

            </td>
            <td style="width:15%" bgcolor="#D0D0D1">
                <p class="s5"
                   style="padding-top: 8pt; padding-bottom: 8pt; padding-left: 6pt;text-indent: 0;text-align: left;">
                    {{__('invoices.services_or_products')}}
                </p>
            </td>
            <td style="width: 20%!important;" bgcolor="#D0D0D1">
                <p class="s5"
                   style="padding-top: 8pt; padding-bottom: 8pt;padding-left: 9pt;text-indent: 0;text-align: left;">
                    {{__('invoices.excerpt')}}
                </p>
            </td>

            <td style="width: 20%!important;" bgcolor="#D0D0D1">
                <p class="s5"
                   style="padding-top: 8pt; padding-bottom: 8pt;padding-left: 9pt;text-indent: 0;text-align: left;">
                    {{__('invoices.expected_quantity')}}
                </p>
            </td>

            <td style="width: 20%!important;" bgcolor="#D0D0D1">
                <p class="s5"
                   style="padding-top: 8pt; padding-bottom: 8pt;padding-left: 9pt;text-indent: 0;text-align: left;">
                    {{__('invoices.delivery_quantity')}}
                </p>
            </td>

        </tr>

        @foreach($order->items as $item)
            <tr style="">
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ $item->created_at->format('Y-m-d') }}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{$item->salesDeliverable->name}}
                    </p>
                </td>
                <td>
                    <div class="s6 excerpt"
                         style="color:black; font-size: 2pt!important; padding-top: 6pt;padding-left: 9pt;text-indent: 0;line-height: 12pt;text-align: left;">
                        {!! $item->excerpt !!}
                    </div>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format($item->expected_quantity, 2, ',', ' ') }}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format($item->quantity, 2, ',', ' ') }}
                    </p>
                </td>

            </tr>
        @endforeach
    </table>

    <table style="width: 100%; margin:25px 0 5px 0">
        <tr>
            <td style="width:100%;border-top-style:dashed;border-top-width:1pt;border-top-color:#B9BEC4">
            </td>
        </tr>
    </table>

@endsection
