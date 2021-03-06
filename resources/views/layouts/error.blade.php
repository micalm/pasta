<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>{{ env('APP_NAME') }} | @yield('title')</title>
    <link rel="stylesheet" href="{!! asset('/css/app.css') !!}">
    <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
    <link rel="manifest" href="/site.webmanifest">
    <link rel="mask-icon" href="/safari-pinned-tab.svg" color="#ebcb8b">
    <meta name="msapplication-TileColor" content="#ffc40d">
    <meta name="theme-color" content="#eceff4">
</head>
<body>
    <div class="error-human">
        <div class="error-container">
            <div class="error-head">
                Oops...
            </div>
            <div class="error-desc">
                <p>
                    @yield('description')
                </p>
            </div>
            <div class="error-link">
                <a href="javascript:history.back()">&larr; Go back</a>
            </div>
        </div>
    </div>
    <div class="error-techie">
        <div class="error-message">
            Error @yield('code') - @yield('message')<br>
            @if (Request::hasHeader('request-id'))
            Request {{ Request::header('request-id') }}
            @endif
        </div>
    </div>
    <script src="{!! mix('js/app.js') !!}"></script>
</body>
</html>
