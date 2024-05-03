<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=Edge"/>
    <meta charset="utf-8"/>
    <link rel="stylesheet" href="{{ url('css/documents-print.css') }}">
    <style class="shared-css" type="text/css">
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        body {
            padding: 40pt 25pt 40px 25pt;
            font-family: Tahoma_w;
            font-size: 8pt;
            line-height: 16px;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .w-full {
            width: 100%;
        }

        .w-50 {
            width: 49.5%;
        }

        .w-80 {
            width: 79.9%;
        }

        .w-20 {
            width: 19.9%;
        }

        .text-gray {
            color: #808080;
        }

        .font-bold {
            font-weight: 800;
            font-family: Tahoma-Bold_s;
        }

        .font-semi-bold {
            font-weight: 600;
            font-family: Tahoma-Bold_s;
        }

        .text-xs {
            font-size: 8pt;
        }

        .text-sm {
            font-size: 9pt;
        }

        .text-md {
            font-size: 10pt;
        }

        .text-lg {
            font-size: 12pt;
        }

        .text-2xl {
            font-size: 20pt;
        }

        .my-2 {
            margin-top: 0.5rem;
            margin-bottom: 0.5rem;
        }

        .text-uppercase {
            text-transform: uppercase;
        }

        .mt-10 {
            margin-top: 1.25rem;
        }

        .py-2 {
            padding-top: 0.5rem;
            padding-bottom: 0.5rem;
        }

        .main-table table thead th {
            border: 1px solid black;
            padding-top: 5pt;
            padding-bottom: 5pt;
        }

        .main-table table tbody td, .main-table table tbody th {
            border: 1px solid black;
            padding-top: 4pt;
            padding-bottom: 4pt;
        }

        .main-table table tfoot th:nth-child(2) {
            border: 1px solid black;
            padding-bottom: 10pt;
            padding-top: 10pt;
        }

        a {
            color: #0a0302;
            text-decoration: none;
        }

        .main-table table tfoot td, .main-table table tfoot th {
            padding-right: 10px;
        }

        .main-table table tbody td, .main-table table tbody th {
            padding-right: 8px;
            padding-left: 8px;
        }

        .main-table table {
            border-collapse: collapse;
            width: 100%;
        }
    </style>
</head>

<body style="margin: 0;">
<div class="">
    <div class="">
        <table class="w-full">
            <tr>
                <td>
                    <img src="{{url('images/mrc-montcalm.png')}}" alt="">
                </td>
                <td class="text-right">
                    <h1 class="text-right text-gray font-bold text-2xl">
                        {{__('invoices.receipt')}} {{ $property->property_number }}
                    </h1>
                </td>
            </tr>

            <tr>
                <td class="text-sm">
                    <p>
                        <strong>
                            {{ $property->organization->name }}
                        </strong>
                    </p>
                    <p>
                        {{ $property->organization->address }}
                    </p>
                    <p>
                        <a href="tel:+1 8198612290">
                            {{ $property->organization->phone }}
                        </a>
                    </p>
                    <p>
                        <a href="mailto:{{ $property->organization->email }}">
                            {{ $property->organization->email }}
                        </a>
                    </p>
                </td>
                <td class="text-right text-uppercase">
                    {{__('invoices.date')}} : {{ $property->transaction_date->format('d/m/Y') }}
                </td>
            </tr>
            <tr>
                <td><br></td>
            </tr>
            <tr class="py-2">
                <td>
                    <span class="font-semi-bold text-sm text-uppercase">
                        {{__('invoices.to')}} :
                    </span> <br>
                    <span class="text-sm">
                        {{ $property->customer }}
                    </span>
                </td>
                <td></td>
            </tr>

            <tr>
                <td><br></td>
            </tr>

            <tr class="">
                <td colspan="2">
                    <strong class="text-semi-bold text-md">
                        {{__('invoices.subject')}}
                        : {{__('invoices.subject_title', ['auction_name' => $property->auction->name])}}
                    </strong>
                </td>
            </tr>

        </table>
        <div class="main-table mt-10">
            <table class="w-full">
                <thead>
                <tr>
                    <th class="text-center text-uppercase w-80">
                        {{__('invoices.'.$property->property_type)}}
                    </th>
                    <th class="text-center text-uppercase w-20">
                        {{__('invoices.amount')}}
                    </th>
                </tr>
                </thead>
                <tbody>

                <tr>
                    <th class="text-left w-80">
                        #{{ $property->property_number }}
                    </th>
                    <td class="text-left w-20"></td>
                </tr>

                <tr style="border-top: solid white 2px!important;">
                    <th class="text-left w-80">
                        {{__('invoices.property_owners')}} :
                        <strong class="text-semi-bold">
                            {{$property->getPropertyOwners()}}
                        </strong>
                    </th>
                    <td class="text-left w-20"></td>
                </tr>

                <tr style="border-top: solid white 2px!important;">
                    <th class="text-left w-80">
                        {{__('invoices.batches')}} :
                        <strong class="text-semi-bold">
                            {{$property->getPropertyBatches()}}
                        </strong>
                    </th>
                    <td class="text-left w-20"></td>
                </tr>

                <tr style="border-top: solid white 2px!important;">
                    <td class="text-right text-md w-80" style="padding-top: 200px;padding-bottom: 200px">
                        {{__('invoices.amount_du')}}:
                    </td>
                    <td class="text-right font-bold w-20">
                        {{ number_format($property->getTotalTaxes(), 2, '.', ' ')}} $
                    </td>
                </tr>
                </tbody>
                <tfoot>
                <tr>
                    <th class="text-right text-uppercase">
                        {{__('invoices.total')}}
                    </th>
                    <th class="text-right">{{ number_format($property->getTotalTaxes(), 2, '.', ' ')}} $</th>
                </tr>
                <tr>
                    <th class="text-right text-uppercase">
                        {{__('invoices.total_received')}}
                    </th>
                    <th class="text-right" style="border-top: solid white 2px!important;">{{ number_format($property->getPaymentTotalReceived(), 2, '.', ' ')}} $</th>
                </tr>
                <tr>
                    <th class="text-right text-uppercase">
                        {{__('invoices.refund_amount')}}
                    </th>
                    <th class="text-right" style="border-top: solid white 2px!important;">{{ number_format($property->getPaymentRefundedAmount(), 2, '.', ' ')}} $</th>
                </tr>

                </tfoot>
            </table>
        </div>
        <div class="text-md mt-10">
            {{
                __('invoices.received_by_and_payment_method', [
                    'received_by' => $property->getPaymentReceiver(),
                    'payment_method' => __('invoices.'. $property->getPaymentMethod())
                ])
            }}
        </div>
    </div>
</div>
</body>
</html>
