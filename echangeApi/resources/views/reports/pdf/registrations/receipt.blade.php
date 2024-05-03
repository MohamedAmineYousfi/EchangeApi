<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{$filename}}</title>
  <style>
    body {
      font-family: "Avenir Thin", sans-serif;
      counter-reset: page;
      box-sizing: inherit;
      background-color: #fff;
    }

    .invoice-wrapper {
      width: 100%;
      margin: 0;
    }

    .invoice-wrapper-item-table {
      width: 100%;
    }

    .invoice-wrapper-item-table table,
    .invoice-wrapper-item-table th,
    .invoice-wrapper-item-table td {
      width: 100%;
      border-collapse: collapse;
    }

    .invoice-wrapper-item-table table tr {
      width: 100%;
    }

    .invoice-wrapper-item-table table tr td {
      padding: 5px 0;
      width: 33.33%;
      font-size: 12px;
    }

    .invoice-wrapper-item-table table td {
      width: 50%;
    }
  </style>
</head>

<body>
  <div class="invoice-wrapper">
    <div style="width: 100%; padding: 30px 0; margin: 0;" class="invoice-wrapper-item-table">
      <table>
        <tbody>
          <tr class="number">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>Reçu #</span>
              <span>{{$registration->code}}</span>
            </td>
          </tr>
          <tr class="date">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>{{$registration->date->locale('fr-FR')->translatedFormat('d F Y')}}</span>
            </td>
          </tr>
          <tr class="total">
            <td style="padding: 30px 0 10px 0;">
              <div>
                <strong>{{$registration->events[0]->name}}</strong>
              </div>
            </td>
            <td></td>
            <td style="padding: 30px 0 10px 0;">
              <div style="text-align: right; padding-bottom: 5px;">
                <span style="padding-right: 5px; text-transform: uppercase;">
                  <strong>Total</strong>
                </span>
                <span style="padding-left: 5px">
                  <strong>{{$registration->getPrice()}} $</strong>
                </span>
              </div>
              <div style="text-align: right; padding: 3px 0;">
                <span>(incluant les taxes)</span>
              </div>
              <div style="text-align: right; padding-top: 5px;">
                <span style="font-style: italic; padding-right: 5px;">
                  Inscription: {{$registration->getPrice()}} $
                </span>
                <span style="font-style: italic; padding-left: 5px">Social: 0 $</span>
              </div>
            </td>
          </tr>
          <tr class="address">
            <td style="padding: 20px 0 10px 0;">
              <div style="text-align: left;max-width: 250px;">{{$registration->user->name}}</div>
              <div style="text-align: left;max-width: 250px;">{{$registration->user->address}}, {{$registration->user->city}}, {{$registration->user->zipcode}}
              </div>
            </td>
            <td></td>
            <td style="padding: 20px 0 10px 0;">
              <div style="width: 100%; text-align: left;">
                <span style="max-width: 200px; display: table;">
                  Patrick Charlebois,
                  MD, Secrétaire
                  Trésorier
                </span>
              </div>
            </td>
          </tr>
          <tr class="copy" style="width: 100%">
            <td style="text-align: left;">
              <div style="padding-left: 20px;">#TPS:GST 106730542</div>
            </td>
            <td style="text-align: center;">
              <div>
                <strong>Copie de l'Association</strong>
              </div>
            </td>
            <td style="text-align: right;">
              <div>#TVQ/QST 1006107652</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <hr/>
    <div style="width: 100%; padding: 30px 0; margin: 0;" class="invoice-wrapper-item-table">
      <table>
        <tbody>
        <tr class="number">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>Reçu #</span>
              <span>{{$registration->code}}</span>
            </td>
          </tr>
          <tr class="date">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>{{$registration->date->locale('fr-FR')->translatedFormat('d F Y')}}</span>
            </td>
          </tr>
          <tr class="total">
            <td style="padding: 30px 0 10px 0;">
              <div>
                <strong>{{$registration->events[0]->name}}</strong>
              </div>
            </td>
            <td></td>
            <td style="padding: 30px 0 10px 0;">
              <div style="text-align: right; padding-bottom: 5px;">
                <span style="padding-right: 5px; text-transform: uppercase;">
                  <strong>Total</strong>
                </span>
                <span style="padding-left: 5px">
                  <strong>{{$registration->getPrice()}} $</strong>
                </span>
              </div>
              <div style="text-align: right; padding: 3px 0;">
                <span>(incluant les taxes)</span>
              </div>
              <div style="text-align: right; padding-top: 5px;">
                <span style="font-style: italic; padding-right: 5px;">
                  Inscription: {{$registration->getPrice()}} $
                </span>
                <span style="font-style: italic; padding-left: 5px">Social: 0 $</span>
              </div>
            </td>
          </tr>
          <tr class="address">
            <td style="padding: 20px 0 10px 0;">
              <div style="text-align: left;max-width: 250px;">{{$registration->user->name}}</div>
              <div style="text-align: left;max-width: 250px;">{{$registration->user->address}}, {{$registration->user->city}}, {{$registration->user->zipcode}}
              </div>
            </td>
            <td></td>
            <td style="padding: 20px 0 10px 0;">
              <div style="width: 100%; text-align: left;">
                <span style="max-width: 200px; display: table;">
                  Patrick Charlebois,
                  MD, Secrétaire
                  Trésorier
                </span>
              </div>
            </td>
          </tr>
          <tr class="copy" style="width: 100%">
            <td style="text-align: left;">
              <div style="padding-left: 20px;">#TPS:GST 106730542</div>
            </td>
            <td style="text-align: center;">
              <div>
                <strong>Copie du Fédéral</strong>
              </div>
            </td>
            <td style="text-align: right;">
              <div>#TVQ/QST 1006107652</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
    <hr/>
    <div style="width: 100%; padding: 30px 0; margin: 0;" class="invoice-wrapper-item-table">
      <table>
        <tbody>
        <tr class="number">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>Reçu #</span>
              <span>{{$registration->code}}</span>
            </td>
          </tr>
          <tr class="date">
            <td></td>
            <td></td>
            <td style="text-align: right;">
              <span>{{$registration->date->locale('fr-FR')->translatedFormat('d F Y')}}</span>
            </td>
          </tr>
          <tr class="total">
            <td style="padding: 30px 0 10px 0;">
              <div>
                <strong>{{$registration->events[0]->name}}</strong>
              </div>
            </td>
            <td></td>
            <td style="padding: 30px 0 10px 0;">
              <div style="text-align: right; padding-bottom: 5px;">
                <span style="padding-right: 5px; text-transform: uppercase;">
                  <strong>Total</strong>
                </span>
                <span style="padding-left: 5px">
                  <strong>{{$registration->getPrice()}} $</strong>
                </span>
              </div>
              <div style="text-align: right; padding: 3px 0;">
                <span>(incluant les taxes)</span>
              </div>
              <div style="text-align: right; padding-top: 5px;">
                <span style="font-style: italic; padding-right: 5px;">
                  Inscription: {{$registration->getPrice()}} $
                </span>
                <span style="font-style: italic; padding-left: 5px">Social: 0 $</span>
              </div>
            </td>
          </tr>
          <tr class="address">
            <td style="padding: 20px 0 10px 0;">
              <div style="text-align: left;max-width: 250px;">{{$registration->user->name}}</div>
              <div style="text-align: left;max-width: 250px;">{{$registration->user->address}}, {{$registration->user->city}}, {{$registration->user->zipcode}}
              </div>
            </td>
            <td></td>
            <td style="padding: 20px 0 10px 0;">
              <div style="width: 100%; text-align: left;">
                <span style="max-width: 200px; display: table;">
                  Patrick Charlebois,
                  MD, Secrétaire
                  Trésorier
                </span>
              </div>
            </td>
          </tr>
          <tr class="copy" style="width: 100%">
            <td style="text-align: left;">
              <div style="padding-left: 20px;">#TPS:GST 106730542</div>
            </td>
            <td style="text-align: center;">
              <div>
                <strong>Copie de Provincial</strong>
              </div>
            </td>
            <td style="text-align: right;">
              <div>#TVQ/QST 1006107652</div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</body>

</html>