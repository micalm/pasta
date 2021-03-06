<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }}</title>
    <link rel="stylesheet" href="{!! asset('/css/app.css') !!}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ebcb8b">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#eceff4">
    <script async defer
        data-website-id="223d80a7-0676-4c32-a130-f433a3844e1b"
        data-do-not-track="true"
        data-domains="pasta.mical.cloud"
        src="https://umami.mical.cloud/umami.js"
    ></script>
</head>
<body>
    @include('app/header')
    @yield('body')
    @include('app/help-modal')
    @include('app/about-modal')
    <div class="modal-overlay"></div>
    <script src="{!! mix('js/vendor.js') !!}"></script>
    <script src="{!! mix('js/manifest.js') !!}"></script>
    <script src="{!! mix('js/app.js') !!}"></script>
</body>
</html>
