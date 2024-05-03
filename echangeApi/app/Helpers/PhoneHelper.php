<?php

namespace App\Helpers;

use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class PhoneHelper
{
    public static function formatPhoneNumber($phoneNumber): string
    {
        $phoneNumberUtil = PhoneNumberUtil::getInstance();

        try {
            $parsedPhoneNumber = $phoneNumberUtil->parse($phoneNumber, null);
            $formattedPhoneNumber = $phoneNumberUtil->format($parsedPhoneNumber, PhoneNumberFormat::INTERNATIONAL);

            return $formattedPhoneNumber;
        } catch (NumberParseException $e) {
            return 'Numéro de téléphone invalide';
        }
    }
}
