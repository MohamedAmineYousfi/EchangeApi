@php 
use App\Models\Registration;
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
  </style>
</head>

<body>
  <div class="invoice-wrapper">
    <div class="invoice-wrapper-title">
      <h2>{{$event->name}}</h2>
      <h2>Association québécoise de chirurgie</h2>
    </div>
    <table>
      <thead>
        <tr style="border-bottom: solid 1px #000">
          <th>Nom</th>
          <th>Nb participation</th>
          <th>Sous Total</th>
          <th>Rabais</th>
          <th>Total</th>
        </tr>
      </thead>
    </table>
    @php 
      $totalPricing = [
        'quantity' => 0,
        'subtotal' => 0,
        'discount' => 0,
        'total' => 0
      ];
    @endphp
    @foreach($event->packages as $pack)
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

        $packPricing = $pack->getPricing();

        $totalPackAtendees = $packPricing['quantity'];

      @endphp
      <h3 style="text-transform: uppercase; font-size: 15px;margin-top: 15px;">
        {{$pack->name}}  ({{$pack->price}} $)
      </h3>
      <table>
        <thead>
          <tr>
            <th>{{$pack->name}}</td>
            <th>{{$packPricing['quantity']}}</td>
            <th>{{number_format($packPricing['subtotal'], 2, ',', ' ')}} $</td>
            <th>{{number_format($packPricing['discount'], 2, ',', ' ')}} $</td>
            <th>{{number_format($packPricing['total'], 2, ',', ' ')}} $</td>
          </tr>
        </thead>
        <tbody>
          @foreach($slots as $slot)
            @php 
              $slotAtendees = $slot['timeSlot']->getEventPackageAttendees($pack->id);
              $atendeesCount = $slotAtendees['timeslots'] + $slotAtendees['addons'] + $slotAtendees['guests'];
              $totalPackAtendees = max([$totalPackAtendees, $atendeesCount]);
            @endphp
            @if($slot['addon'])
              @php 
                $addonPricing = [
                  'quantity' => $atendeesCount,
                  'subtotal' => $slot['addon']->price * $atendeesCount,
                  'discount' => 0,
                ];
                $addonPricing['total'] = $addonPricing['subtotal'] - $addonPricing['discount'];
              @endphp
              <tr>
                <td>{{$slot['timeSlot']->name}}</td>
                <td>{{$atendeesCount}}</td>
                <td>{{number_format($addonPricing['subtotal'], 2, ',', ' ')}} $</td>
                <td>{{number_format($addonPricing['discount'], 2, ',', ' ')}} $</td>
                <td>{{number_format($addonPricing['total'], 2, ',', ' ')}} $</td>
              </tr>
              @php 
                $packPricing['quantity'] = $atendeesCount; 
                $packPricing['subtotal'] = $packPricing['subtotal'] + $addonPricing['subtotal']; 
                $packPricing['discount'] = $packPricing['discount'] + $addonPricing['discount']; 
                $packPricing['total'] = $packPricing['total'] + $addonPricing['total']; 
              @endphp
            @else
              <tr>
                <td>{{$slot['timeSlot']->name}}*</td>
                <td>{{$atendeesCount}}</td>
                <td>0.00 $</td>
                <td>0.00 $</td>
                <td>0.00 $</td>
              </tr>
            @endif
          @endforeach
        </tbody>
        <tfoot>
          <tr class="tr-total">
            <td>Total {{$pack->name}}</td>
            <td>{{$totalPackAtendees}}</td>
            <td>{{number_format($packPricing['subtotal'], 2, ',', ' ')}} $</td>
            <td>{{number_format($packPricing['discount'], 2, ',', ' ')}} $</td>
            <td>{{number_format($packPricing['total'], 2, ',', ' ')}} $</td>
          </tr>
        </tfoot>
      </table>
      @php 
        $totalPricing['quantity'] = $totalPricing['quantity'] + $packPricing['quantity']; 
        $totalPricing['subtotal'] = $totalPricing['subtotal'] + $packPricing['subtotal']; 
        $totalPricing['discount'] = $totalPricing['discount'] + $packPricing['discount']; 
        $totalPricing['total'] = $totalPricing['total'] + $packPricing['total']; 
      @endphp
    @endforeach
    <table class="total-table">
      <tfoot>
        <tr>
          <td>Total</td>
          <td>{{$totalPricing['quantity']}}</td>
          <td>{{number_format($totalPricing['subtotal'], 2, ',', ' ')}} $</td>
          <td>{{number_format($totalPricing['discount'], 2, ',', ' ')}} $</td>
          <td>{{number_format($totalPricing['total'], 2, ',', ' ')}} $</td>
        </tr>
      </tfoot>
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