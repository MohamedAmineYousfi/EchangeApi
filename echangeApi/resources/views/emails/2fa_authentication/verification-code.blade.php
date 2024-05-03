<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<meta http-equiv="Content-Type" content="text/html charset=UTF-8"/>
<html lang="en">

<head></head>

<body style="background-color:#ffffff;font-family:HelveticaNeue,Helvetica,Arial,sans-serif">

<table align="center" role="presentation" cellSpacing="0" cellPadding="0" border="0" width="100%"
       style="max-width:37.5em;background-color:#ffffff;border:1px solid #eee;border-radius:5px;box-shadow:0 5px 10px rgba(20,50,70,.2);margin-top:20px;width:360px;margin:0 auto;padding:68px 0 130px">
    <tr style="width:100%">
        <td>

            <p style="font-size:24px;line-height:23px;color:#000;font-weight:800;letter-spacing:0;margin: 10px 0 25px;font-family:HelveticaNeue,Helvetica,Arial,sans-serif;text-align:center;text-transform:uppercase">
                {{ config('app.name') }}

            </p>
            <p style="font-size:11px;line-height:16px;margin:16px 8px 8px 18px;color:#0a85ea;font-weight:700;font-family:HelveticaNeue,Helvetica,Arial,sans-serif;height:16px;letter-spacing:0;text-transform:uppercase;text-align:left"
            >
                {{ __('notifications.greeting') }} {{$user->firstname}} {{$user->lastname}},
            </p>
            <h1 style="color:#000;display:inline-block;font-family:HelveticaNeue-Medium,Helvetica,Arial,sans-serif;font-size:15px;font-weight:500;line-height:24px;margin-bottom:0;margin-top:0;text-align:center">
                {{ __('notifications.verification_code') }}
            </h1>
            <table
                    style="background:rgba(0,0,0,.05);border-radius:4px;margin:16px auto 14px;vertical-align:middle;width:280px"
                    align="center" border="0" cellPadding="0" cellSpacing="0" role="presentation" width="100%"
            >
                <tbody>
                <tr>
                    <td>
                        <p style="font-size:32px;line-height:40px;margin:0 auto;color:#000;display:inline-block;font-family:HelveticaNeue-Bold;font-weight:700;letter-spacing:6px;padding-bottom:8px;padding-top:8px;width:100%;text-align:center">
                            {{ $code??'1452' }}
                        </p>
                    </td>
                </tr>
                </tbody>
            </table>
            <p style="font-size:15px;line-height:23px;margin:0;color:#444;font-family:HelveticaNeue,Helvetica,Arial,sans-serif;letter-spacing:0;padding:0 40px;text-align:center">
                <strong>
                    {{ __('notifications.code_expires') }}
                </strong>
            </p>
            <p style="margin-top: 15px; font-size:15px;line-height:23px;color:#444;font-family:HelveticaNeue,Helvetica,Arial,sans-serif;letter-spacing:0;padding:0 40px;text-align:center">
                {{ __('notifications.verification_code_instructions') }}
            </p>
        </td>
    </tr>
</table>
<p style="font-size:12px;line-height:23px;margin:0;color:#000;font-weight:800;letter-spacing:0;margin-top:20px;font-family:HelveticaNeue,Helvetica,Arial,sans-serif;text-align:center;text-transform:uppercase">
    {{ config('app.name') }}</p>
</body>

</html>