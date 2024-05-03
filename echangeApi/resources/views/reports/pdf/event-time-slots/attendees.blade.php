<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>{{$filename}}</title>
  <style>
    table,
    th,
    td {
      border: 1px solid;
      text-align: left;
      padding: 5px 5px;
      font-size: 10px;
    }

    table {
      border-collapse: collapse;
    }
  </style>
</head>

<body>
  <div>
    <p style="width: 100%; text-align: center; font-size: 16px">
      {{$eventTimeSlot->eventDate->event->name}}<br/>
    </p>
    <p style="text-align: center; font-size: 10px; margin-top: -10px;">
      {{$eventTimeSlot->eventDate->event->excerpt}}
    </p>
    <h1 style="width: 100%; text-align: center; font-size: 18px">
      Liste des presences pour : {{$eventTimeSlot->name}}<br />
    </h1>
    <p style="text-align: center; font-size: 12px; margin-top: -10px;">
      {{ $eventTimeSlot->eventDate->date->format('d/m/Y') }} : {{ $eventTimeSlot->start_time->format('H:i') }} - {{ $eventTimeSlot->end_time->format('H:i') }}
    </p>
    <p style="text-align: center; font-size: 10px; margin-top: -10px;">
      {{$eventTimeSlot->excerpt}}
    </p>
    <table style="width: 100%; margin-top: 30px">
      <thead>
        <tr>
          <th>Reference</th>
          <th>Nom</th>
          <th>Type de participant</th>
          @if($eventTimeSlot->allow_extra_guests)
          <th>Inscriptions</th>
          <th>Conjoint</th>
          <th>Enfant(s)</th>
          @endif
        </tr>
      </thead>
      @php 
        $guestsCount = 0;
      @endphp
      @foreach ($registrations as $registration)
      @php
      $guests = $registration->guests()->whereHas('eventTimeSlots', function ($query) use ($eventTimeSlot) {
        $query->where('event_time_slot_id', $eventTimeSlot->id);
      });
      $companions = (clone $guests)->where('guest_type', 'COMPANION')->get();
      $children = (clone $guests)->where('guest_type', 'CHILD')->get();

      $guestsCount = $guestsCount + $guests->count();
      @endphp
      <tr>
        <td>{{$registration->code}}</td>
        <td>{{$registration->user->name}}</td>
        <td>{{$registration->role->name}}</td>
        @if($eventTimeSlot->allow_extra_guests)
        <td>{{$guests->count() + 1}}</td>
        <td>
          @if($companions)
            @foreach($companions as $companion)
              {{$companion->name}} 
              @if (!$loop->last) 
                ,
              @endif
            @endforeach
          @endif
        </td>
        <td>
          @if($children)
            @foreach($children as $child)
              {{$child->name}} ({{$child->age}} ans)
              @if (!$loop->last) 
                ,
              @endif
            @endforeach
          @endif
        </td>
        @endif
      </tr>
      @endforeach
      <tfoot>
        @if($eventTimeSlot->allow_extra_guests)
        <tr>
          <th colspan="2">Total des presences membres</th>
          <th colspan="4">{{$registrations->count()}}</th>
        </tr>
        <tr>
          <th colspan="2">Total des presences accompagnants</th>
          <th colspan="4">{{$guestsCount}}</th>
        </tr>
        <tr>
          <th colspan="2">Total des presences</th>
          <th colspan="4">{{$registrations->count() + $guestsCount}}</th>
        </tr>
        @else
        <tr>
          <th colspan="2">Total des presences</th>
          <th colspan="1">{{$registrations->count()}}</th>
        </tr>
        @endif
      </tfoot>
    </table>
  </div>
</body>

</html>