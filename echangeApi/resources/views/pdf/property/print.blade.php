<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lettres Citoyens PDF</title>
    <style>
        body {
          font-family: Calibri, Arial, sans-serif;
          font-size: 10pt;
        }
        .letter-container {
          /* page-break-after: always; */
        }
        .header {
          overflow: auto;
        }
        .logo {
          width: auto;
          height: 70px;
          float: left;
        }
        .address {
          text-align: right;
          float: right;
        }
        .date {
          margin-top: 10px;
          text-align: right;
        }
        .recipient-address {
          margin-top: 20px;
          text-transform: uppercase;
          margin-bottom: 25px;
        }
        .subject {
          font-weight: bold;
          margin-top: 10px;
        }
        .message-body {
          margin-top: 20px;
          text-align: justify;
        }
        .message-body p {
          line-height: 1.5;
          text-indent: 50px;
        }
        .signature {
          position: fixed;
          bottom: 0;
          left: 55%;
          text-align: left;
        }
        .fw-bold {
          font-weight: bold;
        }
        .f-underlined {
          text-decoration: underline;
          text-decoration-color: #000000;
        }
        .text-center {
          text-align: center;
        }
        .separator {
          background-color: #82c145;
          border-color: #82c145;
          height: 3px;
          border-style: none;
        }
        .mb-8 {
          margin-bottom: 8px;
        }
        .mt-16 {
          margin-top: 16px;
        }
        .footer-block {
          border: 1px solid #000000;
          padding: 4px 30px;
          width: 85%;
          margin: 0 auto;
        }
        table {
          width: 100%;
          border-collapse: collapse;
          margin-top: 20px;
          margin-bottom: 20px;
        }

        th, td {
          border: 1px solid #000;
          padding: 8px;
          text-align: center;
          font-weight: bold;
        }
    </style>
</head>
<body>
  @php
    $mrc_info = "<p>Pour ce faire, vous devez effectuer votre paiement à la Cour municipale régionale de Montcalm sise au 1530, rue Albert à Sainte-Julienne. <span class='fw-bold f-underlined'>Les seuls modes de paiement acceptés sont:</span> argent comptant, chèque certifié, traite bancaire ou mandat-poste émis à l'ordre de la MRC de Montcalm. Vous devrez mentionner votre numéro de dossier <span class='fw-bold'>(VT-MAT/2024-001)</span> pour vous assurer que votre paiement soit bien imputé à votre dossier. <span class='fw-bold f-underlined'>Aucun virement bancaire ne sera accepté.</span></p>";

    $open_hours = "<div class='mb-8'>Lundi : 8 h 00 à 12 h et 12 h 45 à 19 h 30</div> <div class='mb-8'>Mardi au jeudi : 8 h 00 à 12 h et 12 h 45 à 16 h 30</div> <div class='mb-8'>Vendredi : 8 h 00 à 12 h.</div>";

    $important_note = "<span>Pour toutes informations, <span class='fw-bold'>veuillez communiquer avec la Cour municipale régionale de Montcalm au (450)831-2182 poste 7029.</span>";

    $sender = "Me Nicolas Rousseau, OMA";

    $sender_role = "Greffier-trésorier adjoint – ventes pour taxes";
  @endphp
  @foreach($properties as $propertyIndex => $property)
    @foreach($property->owners as $ownerIndex => $owner)
      <div class="letter-container">
        <div class="header">
          @if(isset($property->organization->logo))
            <img src="{{ $property->organization->logo }}" class="logo" alt="organization-logo">
          @else
            <p class="logo"></p>
          @endif
            
          <div class="address">
            <!-- Adresse de l'entreprise -->
            <div class="fw-bold">{{ $property->organization->name }}</div>
            <div>{{ $property->organization->address }}</div>
            <div>Tél: {{ $property->organization->phone }}</div>
          </div>
          <div style="clear: both;"></div>
        </div>

        <div class="date">
          <!-- Date -->
          {{ $city }}, le {{ $letterDate }}
        </div>

        <div class="recipient-address">
          <!-- Adresse du destinataire -->
          <p class="fw-bold f-underlined">envoi par poste recommandée</p>
          @if (is_null($owner->firstname) && is_null($owner->lastname))
            {{ $owner->company_name }}
          @else
            {{ $owner->firstname }} {{ $owner->lastname }}
          @endif
          <br>
          {{ $owner->address }}<br>
          {{ $owner->city }} ({{ $owner->state }}) {{ $owner->zipcode }}
        </div>

        <div class="subject">
            <!-- Objet de la lettre -->
            Objet: Lot(s) numéro(s) : 
            @foreach ($property->batch_numbers as $index => $lot)
              {{ $lot['value']}}@if ($index < count($property->batch_numbers) - 1),@endif
            @endforeach
            du cadastre du Québec <br>
            <span style="margin-left: 43px;">(N/D : {{ $property->property_number }})</span>
        </div>

        <hr class="separator">

        <div class="message-body">
          <div>Madame, Monsieur,</div>
          <p>En conformité avec le <span style="font-style: italic">Code municipal du Québec</span> (RLRQ, chapitre C-27.1), votre immeuble nous a été transmis pour fins de vente pour défaut de paiement des taxes foncières.</p>

          <p>En conséquence, prenez avis que <span class="fw-bold">le {{ $property->auction->start_at->translatedFormat('j F Y') }}, à {{ $property->auction->start_at->setTimezone($timezone)->translatedFormat('H:i') }}</span>, au <span class="fw-bold">{{ $property->auction->address }} {{ $property->auction->city }}</span>, <span class="fw-bold">nous procéderons à la vente de votre immeuble, à moins que vous acquittiez, <span class="fw-bold f-underlined">en un seul versement</span>, les taxes, frais et intérêts dus avant cette date.</span></p>

          <p>Pour ce faire, vous devez effectuer votre paiement à la Cour municipale régionale de Montcalm sise au 1530, rue Albert à Sainte-Julienne. <span class='fw-bold f-underlined'>Les seuls modes de paiement acceptés sont:</span> argent comptant, chèque certifié, traite bancaire ou mandat-poste émis à l'ordre de la MRC de Montcalm. Vous devrez mentionner votre numéro de dossier <span class='fw-bold'>({{ $property->property_number }})</span> pour vous assurer que votre paiement soit bien imputé à votre dossier. <span class='fw-bold f-underlined'>Aucun virement bancaire ne sera accepté.</span></p>

          <div class="fw-bold f-underlined text-center mb-8">Heures d’ouverture des bureaux de la Cour municipale</div>
          <div class="text-center">{!! $open_hours !!}</div>

          <div class="mt-16">Les montants des taxes et frais dus s'élèvent à :</div>

          <table>
            <thead>
                <tr>
                    <th>Taxes municipales et intérêts</th>
                    <th>Taxes scolaires et intérêts</th>
                    <th>Frais et intérêts - transfert de dossier</th>
                    <th width="100">TOTAL</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{{ $property->owed_taxes_municipality }} $</td>
                    <td>{{ $property->owed_taxes_school_board }} $</td>
                    <td>{{ $property->mrc_fees }} $</td>
                    <td>{{ $property->mrc_fees + $property->owed_taxes_school_board + $property->owed_taxes_municipality }} $</td>
                </tr>
            </tbody>
          </table>

          <div class="footer-block">
            <div style="color: red" class="fw-bold f-underlined text-center">IMPORTANT</div>
            <span>* Les paiements faits directement en ligne, via votre institution financière, <span class="fw-bold f-underlined">ne seront pas acceptés.</span></span><br><br>
            {!! $important_note !!}
          </div>

        </div>

        <div class="signature">
          <!-- Signature et nom de l'envoyeur -->
          <span style="border-top: 1px solid; padding-top: 10px">{!! $sender !!}</span><br>
          {!! $sender_role !!}
        </div>
      </div>
      @if ($ownerIndex < count($property->owners) - 1 || $propertyIndex < count($properties) - 1)
        <div style="page-break-after:always;"></div>
      @endif
    @endforeach
  @endforeach
</body>
</html>
