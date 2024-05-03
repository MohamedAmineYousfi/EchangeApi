@extends('pdf.layout')


@section('document-type')
    {{__('invoices.sales_orders')}}
@endsection

@section('document-ref')
    {{__('invoices.order')}}
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

            <td style="width:6%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.quantity')}}
                </p>
            </td>

            <td style="width:8%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.amount')}}
                </p>
            </td>
            <td style="width:8%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.dis')}}
                </p>
            </td>

            <td style="width:15%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.tax')}}
                </p>
            </td>

            {{--
            <td style="width:6%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.rate')}}
                </p>
            </td>
            <td style="width:10%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.total')}}
                </p>
            </td>
            --}}
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
                        {{$item->salesOrderable->name}}
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
                        {{ number_format($item->quantity, 0, ',', ' ') }}
                    </p>
                </td>

                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format($item->getItemSubTotalAmount(), 2, ',', ' ') }}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format($item->getItemDiscountAmount(), 2, ',', ' ') }}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        @foreach($order->getOrderTaxes()['details'] as $element)
                            {{ $element['name'] }}
                            @if(!$loop->last)
                                /
                            @endif
                        @endforeach
                        - {{ number_format($item->getItemTaxes()['total'], 2, ',', ' ') }}
                    </p>
                </td>
                {{--
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{$item->getTaxesRate()}}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format($item->getItemTotalAmount(), 2, ',', ' ') }}
                    </p>
                </td>
                --}}
            </tr>
        @endforeach
    </table>

    <table style="width: 100%; margin:25px 0 5px 0">
        <tr>
            <td style="width:100%;border-top-style:dashed;border-top-width:1pt;border-top-color:#B9BEC4">
            </td>
        </tr>
    </table>

    <table style="border-collapse:collapse; width: 100%" cellspacing="0">
        <tr style="height:39pt">
            <td style="width:114pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:165pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:106pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
                <p class="s7" style="padding-top: 10pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.amount')}}
                </p>
            </td>
            <td style="width:127pt" colspan="2">
                <p style="text-indent: 0;text-align: left;"><br/></p>
                <p class="s6" style="padding-top: 10pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                    {{ number_format($order->getOrderSubTotalAmount(), 2, ',', ' ') }}
                </p>
            </td>
        </tr>

        @if($order->getOrderTaxes())
            @foreach($order->getOrderTaxes()['details'] as $item)
                <tr style="height:20pt">
                    <td style="width:114pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
                    <td style="width:165pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
                    <td style="width:106pt">
                        <p class="s7" style="padding-top: 4pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                            {{$item['name']}} @ {{$item['value']}}
                        </p>
                    </td>
                    <td style="width:127pt" colspan="2">
                        <p class="s6" style="padding-top: 4pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                            {{ number_format($item['amount'], 2, ',', ' ') }}
                        </p>
                    </td>
                </tr>
            @endforeach
        @endif

        <tr style="height:24pt;">
            <td style="width:114pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:165pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:106pt;">
                <p class="s7" style="padding-top: 4pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.total')}}
                </p>
            </td>
            <td style="width:200pt;"
                colspan="2">
                <p class="s6" style="padding-top: 4pt; padding-right: 4pt; text-indent: 0; text-align: right;">
                    {{ number_format($order->getOrderTaxes()['total'], 2, ',', ' ') }}
                </p>
            </td>
        </tr>

        <tr>
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt">
                <p class="s7" style="padding-top: 4pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.discount')}}
                </p>
            </td>
            <td style="width:1pt" colspan="2">
                <p class="s6" style="padding-top: 4pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                    {{ number_format($order->getOrderDiscounts()['total'], 2, ',', ' ') }}
                </p>
            </td>
        </tr>

        <tr>
            <td colspan="">
                <br>
                <p></p>
            </td>
        </tr>

        <tr style="">
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt;border-top-style:dashed;border-top-width:1pt;border-top-color:#B9BEC4">
                <p style="text-indent: 0;text-align: left;"><br/></p>
                <p class="s7" style="text-transform: uppercase; padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.balance_due')}}
                </p>
            </td>
            <td style="width:1pt;border-top-style:dashed;border-top-width:1pt;border-top-color:#B9BEC4" colspan="3">
                <p class="s8" style="padding-top: 9pt;text-indent: 0;text-align: right;">
                    {{ number_format($order->getOrderTotalAmount(), 2, ',', ' ') }} $
                </p>
            </td>
        </tr>
        <tr>
            <td><p><br></p></td>
        </tr>

        <tr style="height:32pt; padding-top: 10pt">
            <td style="width:200pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
                <p class="s4"
                   style="padding-top: 9pt;font-weight: bold; font-size: 17px; padding-left: 2pt;text-indent: 0;line-height: 9pt;text-align: left;">
                    {{__('invoices.taxes_summary')}}
                </p>
            </td>
            <td style="width:165pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:106pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:67pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:127pt" colspan="2">

            </td>
        </tr>
    </table>


    @if($order->getOrderTaxes())
        <table style="border-collapse:collapse; margin-top: 10px; width: 100%" cellspacing="0">
            <tr style="height:19pt">
                <td style="width:20%" bgcolor="#E1E2E2">
                    <p style="text-indent: 0;text-align: left;"><br/></p>
                </td>
                <td style="width:30%" bgcolor="#E1E2E2">
                    <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                        {{__('invoices.tax')}}
                    </p>
                </td>
                <td style="width:25%" bgcolor="#E1E2E2">
                    <p style="text-indent: 0;text-align: left;"><br/></p>
                </td>
                <td style="width:20%" bgcolor="#E1E2E2">
                    <p class="s4"
                       style="padding-top: 8pt; padding-bottom: 8pt;padding-left: 1pt;text-indent: 0;text-align: left;">
                        {{__('invoices.rate')}}
                    </p>
                </td>
                <td style="width:25%" bgcolor="#E1E2E2">
                    <p class="s4"
                       style="padding-top: 8pt; padding-bottom: 8pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                        {{__('invoices.net')}}
                    </p>
                </td>
            </tr>


            @foreach($order->getOrderTaxes()['details'] as $item)
                <tr style="">
                    <td>
                        <p style="text-indent: 0;text-align: left;"><br/></p>
                    </td>
                    <td>
                        <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                            {{$item['name']}}
                        </p>
                    </td>
                    <td>
                        <p style="text-indent: 0;text-align: left;"><br/></p>
                    </td>
                    <td>
                        <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                            {{$item['value']}}
                        </p>
                    </td>
                    <td>
                        <p class="s6" style="padding-top: 8pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                            {{ number_format($item['amount'], 2, ',', ' ') }}
                        </p>
                    </td>
                </tr>
            @endforeach
        </table>
    @endif
@endsection