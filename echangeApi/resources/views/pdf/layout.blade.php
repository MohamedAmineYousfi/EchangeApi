<!DOCTYPE  html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{$filename??''}}</title>
    <style type="text/css">
        @page {
            margin: 0;
        }

        * {
            margin: 0;
            padding: 0;
            text-indent: 0;
        }

        body {
            margin: 20pt 15pt 20px 15pt;
        }

        .s1 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 6.5pt;
        }

        a {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 5pt;
            line-height: 8pt;
        }

        .s2 {
            color: #151B1B;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 12.5pt;
        }

        .s3 {
            color: #8C8F95;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s4 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s5 {
            color: #151B1B;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
        }

        .s6 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 7pt;
            line-height: 11pt;
        }

        .excerpt p {
            color: black !important;
            font-size: 7pt !important;
            line-height: 11pt !important;
        }

        .s7 {
            color: #8C8F95;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 9pt;
        }

        .s8 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 12.5pt;
        }

        .s9 {
            color: black;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: bold;
            text-decoration: none;
            font-size: 25pt;
        }

        p {
            color: #8C8F95;
            font-family: Arial, sans-serif;
            font-style: normal;
            font-weight: normal;
            text-decoration: none;
            font-size: 8pt;
            margin: 0;
        }

        table, tbody {
            vertical-align: top;
            overflow: visible;
        }

        footer {
            position: fixed;
            bottom: 0;
            left: 15pt;
            right: 0;
            height: 60px;
        }

        .page {
            page-break-after: always;
        }
    </style>
</head>
<body>

<footer style="width: 100%;" id="footer">
    <p style="text-indent: 0;line-height: 120%;text-align: left;">
        Nº d’inscription à la TPS/TVH: 810858761RT0001
    </p>
    <p style="text-indent: 0;line-height: 120%;text-align: left;">
        Nº d’enregistrement de la TVQ : 1222861132TQ000
    </p>
</footer>

<div class="" style="padding-bottom: 70px">

    <table style="margin-top: 1px; width: 100%;">
        <tr>
            <td style="width: 70%">
                <div style="display: inline-block;padding-left:5.5pt; align-self: flex-start; width: 50%; height: 150px;">
                    <p style="padding-top: 1pt; text-indent: 0; text-align: left;">
            <span style="color: black; font-family: Arial, sans-serif; font-style: normal; font-weight: bold; text-decoration: none; font-size: 20px;">
                {{ $order->organization->name }}
            </span>
                    </p>
                    <p class="s1" style="padding-top: 6pt; text-indent: 0; text-align: left; font-size: 14px">
                        {{ $order->organization->address }}
                    </p>
                    <p class="s1" style="padding-top: 4pt; text-indent: 0; text-align: left; font-size: 14px">
                        <a href="tel:+1 8198612290" style=" font-size: 14px">
                            {{ $order->organization->phone }}
                        </a>
                    </p>
                    <p style="padding-top: 3pt; text-indent: 0; text-align: left; font-size: 14px">
                        <a href="mailto:{{ $order->organization->email }}" style=" font-size: 14px">
                            {{ $order->organization->email }}
                        </a>
                    </p>
                </div>
            </td>
            <td style="width: 30%">
                <div style="display: inline-block; min-height: 200px; width: 95%; align-self: flex-start; transform: translateX(-25px);">
                    <img src="{{ $order->organization->logo }}" style="text-align: center; width: 120%; margin: auto"
                         alt="logo">
                </div>
            </td>
        </tr>
    </table>

    <table style="margin-left:5.5pt; width: 100%" class="">
        <tr>
            <td style="width: 65%">
                <div class="" style="">
                    <p class="s2" style="padding-top: 1pt;padding-left: 2pt;text-indent: 0;text-align: left;">
                        @yield('document-type')
                    </p>
                    <p class="s3"
                       style="padding-top: 5pt;padding-left: 2pt;text-indent: 0;text-align: left;text-transform: uppercase">
                        @yield(('bill_to_text'))
                    </p>
                    @yield('bill_to')
                </div>
            </td>
            <td style="width: 35%;">
                <table style="border-collapse:collapse;" cellspacing="0">
                    <tr style="height:16pt">
                        <td style="" colspan="3">
                            <p class="s3" style="padding-top: 5pt;text-indent: 0;text-align: left;">
                                @yield('document-ref')
                            </p>
                        </td>
                        <td style="width:127pt" colspan="2">
                            <p class="s4" style="padding-top: 5pt;padding-left: 21pt;text-indent: 0;text-align: left;">
                                {{ $order->code }}
                            </p>
                        </td>
                    </tr>
                    <tr style="height:13pt">
                        <td style="" colspan="3">
                            <p class="s3"
                               style="padding-top: 2pt;text-indent: 0;line-height: 9pt;text-align: left;">
                                {{__('invoices.date')}}
                            </p>
                        </td>
                        <td style="width:127pt" colspan="2">
                            <p class="s4"
                               style="padding-top: 2pt;padding-left: 21pt;text-indent: 0;line-height: 9pt;text-align: left;">
                                {{ \Carbon\Carbon::now()->format('Y-m-d') }}
                            </p>
                        </td>
                    </tr>
                    @if(isset($order->expiration_time))
                        <tr style="">
                            <td style="" colspan="3">
                                <p class="s3" style="padding-top: 2pt;text-indent: 0;text-align: left;">
                                    {{__('invoices.due_date')}}
                                </p></td>
                            <td style="width:127pt" colspan="2">
                                <p class="s4"
                                   style="padding-top: 2pt;padding-left: 21pt;text-indent: 0;text-align: left;">
                                    {{ $order->expiration_time->format('Y-m-d') }}
                                </p>
                            </td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>

    </table>

    @yield('content')

</div>

</body>
</html>
