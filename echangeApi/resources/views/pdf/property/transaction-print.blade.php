@extends('pdf.property.layout')


@section('document-type')
    {{__('invoices.receipt')}}
@endsection

@section('document-ref')
    {{__('invoices.folder')}}
@endsection

@section('bill_to_text')
    {{__('invoices.bill_to_2')}}
@endsection

@section('bill_to')
    <p class="s4" style="padding-top: 2pt;padding-left: 2pt;text-indent: 0;line-height: 9pt;text-align: left;">
        {{$property->customer}}
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
                    {{__('invoices.excerpt')}}
                </p>
            </td>

            <td style="width:8%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left; padding-left: 8px">
                    {{__('invoices.amount')}}
                </p>
            </td>

            <td style="width:6%" bgcolor="#D0D0D1">
                <p class="s5" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.quantity')}}
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
            <tr style="">
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ $property->transaction_date->format('d/m/Y') }}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{$property->designation}}
                    </p>
                </td>
                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left; padding-left: 8px">
                        {{ number_format($property->getTotal(), 2, ',', ' ') }}
                    </p>
                </td>

                <td>
                    <p class="s6" style="padding-top: 8pt;text-indent: 0;text-align: left;">
                        {{ number_format(1, 0, ',', ' ') }}
                    </p>
                </td>
            </tr>
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
                    {{__('invoices.total_received')}}
                </p>
            </td>
            <td style="width:127pt" colspan="2">
                <p style="text-indent: 0;text-align: left;"><br/></p>
                <p class="s6" style="padding-top: 10pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                    {{ number_format($property->getPaymentTotalReceived(), 2, '.', ' ')}}
                </p>
            </td>
        </tr>


        <tr style="height:24pt;">
            <td style="width:114pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:165pt">
                <p style="text-indent: 0;text-align: left;"><br/></p>
            </td>
            <td style="width:106pt;">
                <p class="s7" style="padding-top: 4pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.refund_amount')}}
                </p>
            </td>
            <td style="width:200pt;"
                colspan="2">
                <p class="s6" style="padding-top: 4pt; padding-right: 4pt; text-indent: 0; text-align: right;">
                    {{ number_format($property->getPaymentRefundedAmount(), 2, '.', ' ')}}
                </p>
            </td>
        </tr>

        <tr>
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:1pt">
                <p class="s7" style="padding-top: 4pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.amount_paid')}}
                </p>
            </td>
            <td style="width:1pt" colspan="2">
                <p class="s6" style="padding-top: 4pt;padding-right: 4pt;text-indent: 0;text-align: right;">
                    {{ number_format($property->getTotal(), 2, '.', ' ')}}
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
                    {{ number_format(0 , 2, '.', ' ')}} $
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
                    {{__('invoices.more_infos')}}
                </p>
            </td>
            <td style="width:165pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:106pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:67pt"><p style="text-indent: 0;text-align: left;"><br/></p></td>
            <td style="width:127pt" colspan="2">
            </td>
        </tr>
    </table>

    <table style="border-collapse:collapse; margin-top: 10px; width: 100%" cellspacing="0">
        <tr style="height:19pt">
            <td style="width:20%" bgcolor="#E1E2E2">
                <p class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.subject')}}
                </p>
            </td>
            <td style="width:80%" bgcolor="#E1E2E2">
                <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.subject_title', ['auction_name' => $property->auction->name])}}
                </p>
            </td>
        </tr>
        <tr style="height:19pt">
            <td style="width:20%">
                <p class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.property_type')}}
                </p>
            </td>
            <td style="width:80%">
                <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{__('invoices.'.$property->property_type)}}
                </p>
            </td>
        </tr>
        <tr style="height:19pt">
            <td style="width:20%" bgcolor="#E1E2E2">
                <p class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.property_number')}}
                </p>
            </td>
            <td style="width:80%" bgcolor="#E1E2E2">
                <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    #{{ $property->property_number }}
                </p>
            </td>
        </tr>
        <tr style="height:19pt">
            <td style="width:20%">
                <p class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.property_owners')}}
                </p>
            </td>
            <td style="width:80%">
                <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{$property->getPropertyOwners()}}
                </p>
            </td>
        </tr>
        <tr style="height:19pt">
            <td style="width:20%" bgcolor="#E1E2E2">
                <p class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left;">
                    {{__('invoices.batches')}}
                </p>
            </td>
            <td style="width:80%" bgcolor="#E1E2E2">
                <p class="s4" style="padding-top: 8pt; padding-bottom: 8pt;text-indent: 0;text-align: left;">
                    {{$property->getPropertyBatches()}}
                </p>
            </td>
        </tr>
    </table>
    <div  class="s7" style="padding-top: 8pt;padding-left: 3pt;text-indent: 0;text-align: left; margin-top: 25px">
        {{
            __('invoices.received_by_and_payment_method', [
                'received_by' => $property->getPaymentReceiver(),
                'payment_method' => __('invoices.'. $property->getPaymentMethod())
            ])
        }}
    </div>
@endsection