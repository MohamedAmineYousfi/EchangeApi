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
                    <h1>Bonjour {{$registration->user->name}},</h1>
                    <p>Vous avez complété votre inscription à l'évènement {{$registration->events[0]->name}}.</p>
                    <table class="purchase" width="100%" cellpadding="0" cellspacing="0">
                      <tr>
                        <td>
                          <h3>{{$registration->code}}</h3>
                        </td>
                      </tr>
                      <tr>
                        <td colspan="2">
                          @foreach ($registration->events as $event)
                          <table class="purchase_content" width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                              <th class="purchase_heading" align="left">
                                <p class="f-fallback">{{$event->name}}</p>
                                <p class="f-fallback">{{$event->excerpt}}</p>
                              </th>
                            </tr>
                            @foreach ($event->eventDates as $eventDate)
                            <tr>
                              <td width="70%" class="purchase_item">
                                <span class="f-fallback">{{$eventDate->date->format('j F, Y')}}</span> <br>
                                @foreach ($eventDate->eventTimeSlots as $slot)
                                  @if(isset($registrationMappedSlots[$slot->id]))
                                    @php
                                      $registrationSlot = $registrationMappedSlots[$slot->id];
                                    @endphp
                                    <span class="f-fallback item_excerpt" style="padding-left: 20px;">
                                      {{$registrationSlot->start_time->format('H:i')}}
                                      -
                                      {{$registrationSlot->end_time->format('H:i')}}
                                      :
                                      {{$registrationSlot->name}}
                                    </span><br>
                                  @endif
                                  @if(isset($registrationMappedAddons[$slot->id]))
                                    @php
                                      $registrationAddon = $registrationMappedAddons[$slot->id];
                                    @endphp
                                    <span class="f-fallback item_excerpt" style="padding-left: 20px;">
                                      {{$registrationAddon->eventTimeSlot->start_time->format('H:i')}}
                                      -
                                      {{$registrationAddon->eventTimeSlot->end_time->format('H:i')}}
                                      :
                                      {{$registrationAddon->eventTimeSlot->name}}
                                    </span><br>
                                  @endif
                                  @foreach ($registration->guests as $guest)
                                    @if($guest->eventTimeSlots->contains('id', $slot->id))
                                      <span class="f-fallback item_excerpt" style="padding-left: 40px;">
                                        {{$guest->name}}
                                      </span><br>
                                    @endif
                                  @endforeach
                                @endforeach
                              </td>
                            </tr>
                            @endforeach
                          </table>
                          @endforeach
                        </td>
                      </tr>
                    </table>

                    <p>Si vous avez des questions concernant cette facture, veuillez contacter notre <a href="mailto:info@chirurgiequebec.ca">équipe d'assistance</a> pour obtenir de l'aide.</p>
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