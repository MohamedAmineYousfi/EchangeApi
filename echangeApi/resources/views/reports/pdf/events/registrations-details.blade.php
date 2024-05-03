@php 
use App\Models\ResellerInvoice;
@endphp

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$filename}}</title>
  <style>
    @page {
      margin: 0 2em;
    }

    body {
      font-family: "Avenir Thin", sans-serif;
      counter-reset: page;
      box-sizing: inherit;
      background-color: #fff;
    }

    ul {
      padding: 0;
      margin: 0;
      display: inline-block;
    }

    ul li {
      float: left;
      list-style: none;
      font-size: 12px;
      line-height: 18px;
    }

    h1,
    h2,
    h3,
    h4,
    h4,
    h5,
    h6 {
      margin: 0;
    }

    p {
      margin: 0;
      font-size: 12px;
      line-height: 18px;
    }

    .invoice-wrapper {
      width: 100%;
      height: auto;
      display: block;
      margin: 60px 0;
    }

    .invoice-wrapper-title h3 {
      font-size: 20px;
    }

    .invoice-wrapper-item {
      padding: 5px 0;
      border-bottom: solid 1Px #000;
      margin-bottom: 50px;
    }

    .invoice-wrapper-item-name {
      margin-top: 15px;
    }

    .invoice-wrapper-item-name h3 {
      font-weight: 600;
      text-transform: uppercase;
      font-size: 16px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }

    table.main-table {
      margin-bottom: 20px;
    }

    thead tr th {
      font-weight: 600;
      padding: 5px 0;
      font-size: 12px;
    }

    thead tr th:last-child {
      text-align: right;
    }

    .first-head-line-left {
      text-align: left;
      font-size: 14px;
    }

    .first-head-line-right {
      text-align: right;
      font-size: 14px;
    }

    thead tr th:nth-child(1),
    tbody tr td:nth-child(1) {
      text-align: left;
    }

    thead .table-first-head th {
      border-bottom: solid 1Px rgba(0, 0, 0, .2);
    }

    tbody tr td:nth-child(2),
    tbody tr td:nth-child(3) {
      text-align: center;
    }

    tbody tr td:nth-child(4),
    tbody tr td:nth-child(5) {
      text-align: center;
    }

    tbody tr td:last-child {
      text-align: right;
    }

    tbody tr td {
      padding: 5px;
      font-size: 12px;
      border-top: solid 1px #000;
    }

    .footer-table tr td {
      font-weight: 600;
      border-top: solid 1Px rgba(0, 0, 0, .2);
      border-bottom: solid 1Px rgba(0, 0, 0, .2);
    }

    .invoice-wrapper-item-note p {
      font-weight: 600;
      font-style: italic;
      margin-top: 5px;
    }

    .invoice-wrapper-item-note ul {
      width: 100%;
      padding-left: 40px;
    }

    .invoice-wrapper-item-note ul li {
      margin-right: 5px;
    }

    .invoice-wrapper-item-note ul li:last-child {
      margin-right: 0;
    }
  </style>
</head>

<body>
  <div class="invoice-wrapper">
    <div class="invoice-wrapper-title">
      <h3>{{$event->name}}</h3>
    </div>
    @foreach ($registrations as $registration)
    <div class="invoice-wrapper-item">
      <div class="invoice-wrapper-item-name">
        <h3>{{$registration->user->name}}</h3>
      </div>

      <table class="main-table-packs">
        <thead>
          @foreach ($registration->eventPackages as $pack)
            <tr class="table-first-head">
              <th class="first-head-line-left" colspan="2">{{$pack->name}}</td>
              <th class="first-head-line-right" colspan="2">{{$pack->price}}$</td>
            </tr>
          @endforeach
        </thead>
      </table>

      <table class="main-table">
        <thead>
          <tr>
            <th>Plages</td>
            <th>Date</td>
            <th>Heures</td>
            <th>Prix</td>
          </tr>
        </thead>
        <tbody>
          @php 
            $slots = [];
            foreach($pack->eventTimeSlots as $slot) {
              $slots[$slot->id] = ['addon' => null, 'timeSlot' => $slot];
            }
            foreach($pack->addons as $addon) {
              $slots[$addon->eventTimeSlot->id] = ['addon' => $addon, 'timeSlot' => $addon->eventTimeSlot];
            }
            usort($slots, function($a, $b) {
              return $a['timeSlot']->start_time > $b['timeSlot']->start_time ? 1 : -1;
            });
          @endphp

          @foreach ($slots as $slot)
            <tr>
              <td>{{$slot['timeSlot']->name}}</td>
              <td>{{$slot['timeSlot']->start_time->format('d/m/Y')}}</td>
              <td>
                {{$slot['timeSlot']->start_time->format('H:i')}} - {{$slot['timeSlot']->end_time->format('H:i')}}
              </td>
              @if($slot['addon']) 
                <td>{{$slot['addon']->price}}</td>
              @else
                <td>0.00$</td>
              @endif
            </tr>
          @endforeach
        </tbody>
      </table>

      @php
        $invoice = $registration->getActiveInvoices()->where('status', ResellerInvoice::STATUS_PAID)->first();
      @endphp
      <table class="footer-table">
        <tr>
          <td>Frais additionnels</td>
          <td>0.00$</td>
          <td>Rabais</td>
          <td>0.00$</td>
          <td>Grand total</td>
          <td>{{$invoice->getInvoiceTotalAmount()}}$</td>
          <td>Paiement reçu</td>
          <td>{{$invoice->getInvoiceTotalPaied()}}$</td>
          <td>Montant dû</td>
          <td>({{bcsub($invoice->getInvoiceTotalAmount(), $invoice->getInvoiceTotalPaied(), 2)}}$)</td>
        </tr>
      </table>
      <div class="invoice-wrapper-item-note">
        <p>Note:</p>
        <ul>
          <li>No transactions: {{$invoice->code}}</li>
          @foreach($invoice->payments as $payment)
            <li>{{$payment->transaction_id?$payment->transaction_id:$payment->code}}</li>
          @endforeach
        </ul>
      </div>
    </div>
    @endforeach

  </div>
</body>

</html>