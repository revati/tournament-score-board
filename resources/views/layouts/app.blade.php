<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <style media="screen">
        .error {
            color: red;
        }

        .ignored {
            background: #121212;
        }

        table.division td {
            border: solid 1px; #000;
            min-width: 25px;
        }

        table.division td.pending_game {
            border-color: #1d4dc6;
        }

        .playoff {
            float: left;
        }
    </style>
</head>
<body>
    <div>
        <a href="{{ route('tournaments.index') }}"> Tournamets list</a>
        <hr>
    </div>

    @yield('content')
</body>
</html>
