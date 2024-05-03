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
      font-weight: 600;
    }

    .invoice-wrapper-content {
      margin: 30px 0 0 0;
    }

    .invoice-wrapper-content p {
      font-weight: 600;
      font-size: 12px;
      padding-bottom: 12px;
      border-bottom: solid 2px #000;
    }

    table {
      margin-top: 15px;
    }

    table thead tr th {
      font-weight: 600;
      text-align: left;
      padding: 5px 0;
    }

    table thead tr th:last-child {
      padding-left: 60px;
    }

    table tbody tr td {
      padding: 8px 0;
    }

    table tbody tr td:nth-child(1) {
      min-width: 100px;
    }

    table tbody tr td:nth-child(2) {
      min-width: 200px;
      text-align: left;
    }
    
    table tbody tr td:nth-child(3) {
      min-width: 200px;
      text-align: left;
    }

    table tbody tr td:nth-child(4) {
      width: 100%;
      border-bottom: solid 1px #ddd;
    }

    p.total {
      border-top: solid 1px #ddd;
      border-bottom: none;
      display: inline;
      padding: 10px 10px 0 0;
      margin: 30px 0 0 0;
    }
  </style>
</head>

<body>
  <div class="invoice-wrapper">
    <div class="invoice-wrapper-title">
      <h2>{{$event->name}}</h2>
      <h2>Association québécoise de chirurgie</h2>
    </div>
    <hr>
    <div class="invoice-wrapper-content">
      <p>Veuillez signer pour recevoir votre accréditation :</p>
      <table>
        <thead>
          <tr>
            <th colspan="2">Participants</th>
            <th>Type</th>
            <th>Signature</th>
          </tr>
        </thead>
        <tbody>
          @foreach($atendees as $atendee)
            <tr>
              <td> {{$atendee['id']}} </td>
              <td> {{$atendee['name']}} </td>
              <td> {{$atendee['type']}} </td>
              <td> </td>
            </tr>
          @endforeach
        </tbody>
      </table>
      <hr style="display: block; height: 1px; border: 0; border-top: 1px solid #ddd; margin: 25px 0; padding: 0;">
      <p class="total">nombre total de fichiers: {{count($atendees)}}</p>
    </div>
  </div>
</body>

</html>