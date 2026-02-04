<!DOCTYPE html>
<html lang="ru">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title> Basis </title>

        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bulma@1.0.4/css/bulma.min.css"
            >
        @routes

        <script>
            var basis = {};
            basis.token = "{{ csrf_token() }}";
        </script>
        @vite(['resources/css/app.scss', 'resources/js/app.js'])
    </head>
    <body  @class([
        'dark-style' => $user ? $user->dark_style : false,
        "light-style" => $user ? !$user->dark_style : false
        ])> 

        <div id="app">
        </div>
    </body>
</html>
