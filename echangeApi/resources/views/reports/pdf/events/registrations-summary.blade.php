@php 
use App\Models\Registration;
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
      font-size: 12px;
    }

    ul {
      padding: 0;
      margin: 0;
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

    .invoice-wrapper-title {
      text-align: center;
    }

    .invoice-wrapper-title h2 {
      font-size: 18px;
      line-height: 22px;
      font-weight: normal;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 5px;
    }

    table thead tr th {
      font-weight: 600;
      padding: 5px 0;
      font-size: 13px;
      text-transform: uppercase;
      border-bottom: solid 1px #000;
      border: none;
      text-align: center;
    }

    table thead tr th,
    table tbody tr td,
    table tfoot tr td {
      min-width: 100px;
      max-width: 130px;
      font-size: 12px;
    }

    table thead tr th:first-child,
    table tbody tr td:first-child,
    table tfoot tr td:first-child {
      text-align: left;
    }

    table thead tr th:last-child,
    table tbody tr td:last-child,
    table tfoot tr td:last-child {
      text-align: right;
    }

    table thead tr th:last-child {
      text-align: right;
    }

    table tbody tr td,
    table tfoot tr td {
      text-align: center;
      padding: 5px;
      font-size: 12px;
    }

    table tbody tr td:first-child,
    table thead tr th:first-child,
    table tfoot tr td:first-child {
      padding-left: 0;
      min-width: 220px;
    }

    table tbody tr td:last-child,
    table tfoot tr td:last-child {
      padding-right: 0;
    }

    table tfoot tr td {
      font-weight: 600;
      text-transform: uppercase;
    }

    .total-table {
      background-color: #ddd;
      margin-top: 15px;
    }

    .participants-details {
      margin-top: 10px;
    }

    .participants-details-wrapper table {
      border: solid 1px #ddd;
      border-collapse: collapse;
      max-width: 130px;
      margin-top: 10px;
    }

    .participants-details-wrapper table tr td:nth-child(1) {
      padding-left: 5px;
    }

    .participants-details-wrapper table tr td:last-child {
      max-width: 50px;
      min-width: 50px;
      text-align: center;
    }

    table.main-table tr {
      border-bottom: solid 1px #ddd;
    }

  </style>
</head>

<body>
  <div class="invoice-wrapper">
    <div class="invoice-wrapper-title">
      <h1>Liste sommaire d'inscriptions</h1>
      <h2>{{$event->name}}</h2>
      <h2>Association québécoise de chirurgie</h2>
    </div>
    <br><br>
    <table class="main-table">
      <thead>
        <tr>
          <th>Nom</td>
          <th>Type</td>
          <th>Invites</td>
          <th>Total</td>
          <th>Paiement recu</td>
          <th>Montant du</td>
        </tr>
      </thead>
      @php
        $registrations = $event->registrations()
          ->where('status', Registration::STATUS_COMPLETED)
          ->get()
          ->sortBy(function($registration) { 
              return $registration->user->name;
          });
      @endphp
      @foreach ($registrations as $registration)
        @php
          $invoice = $registration->getActiveInvoices()->where('status', ResellerInvoice::STATUS_PAID)->first();
        @endphp
        <tbody>
          <tr>
            <td>{{$registration->user->name}}</td>
            <td>{{$registration->role->name}}</td>
            <td>{{$registration->guests()->count()}}</td>
            <td>{{$invoice->getInvoiceTotalAmount()}} $</td>
            <td>{{$invoice->getInvoiceTotalPaied()}} $</td>
            <td>{{bcsub($invoice->getInvoiceTotalAmount(), $invoice->getInvoiceTotalPaied(), 2)}} $</td>
          </tr>
        </tbody>
      @endforeach
    </table>

    <br><br><br><br>
    <div class="participants-details">
      <h4>Détails sur le type de participant</h4>
      @php
        $roles = Registration::where('status', Registration::STATUS_COMPLETED)
          ->whereHas('events',function ($query) use ($event) {
            $query->where('events.id', $event->id);
          })
          ->join('roles', 'registrations.role_id', '=', 'roles.id')
          ->groupBy('roles.id')
          ->select(DB::raw('roles.name as role'), DB::raw('count(*) as atendees'))
          ->get();
      @endphp
      <div class="participants-details-wrapper">
        <table>
          <tbody>
            @foreach($roles as $role)
              <tr>
                <td>{{$role['role']}}</td>
                <td>{{$role['atendees']}}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
  </div>
</body>

</html>