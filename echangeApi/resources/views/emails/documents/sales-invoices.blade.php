
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Invoice Message</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
        }

        h1 {
            color: #333333;
        }

        p {
            color: #666666;
        }

        a {
            color: #007BFF;
            text-decoration: none;
        }

        .button {
            display: inline-block;
            font-size: 14px;
            padding: 10px 20px;
            color: #ffffff;
            background-color: #007BFF;
            text-decoration: none;
            border-radius: 4px;
        }

        .container p {
            padding: 0!important;
            margin: 0!important;
        }
    </style>
</head>
<body>
<div class="container">
    <h4>
        {{$subject??''}}
    </h4>
    {!! $content??'' !!}
    <p>
        {{ config('app.name') }}
    </p>
</div>
</body>
</html>
