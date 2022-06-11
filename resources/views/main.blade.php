<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Content-Language" content="en">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>@yield('tab_title')</title>
    <meta name="viewport"
          content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no"/>

    <!-- Disable tap highlight on IE -->
    <meta name="msapplication-tap-highlight" content="no">
    <link rel="stylesheet" href="/app.css">

</head>
<body>
    <header>
        <div class="top-menu">
            <div class="message-box">
                @if(session('alert'))
                    <span class="alert alert-{{session('alert')['type']}}">
                        {{ session('alert')['message'] }}
                    </span>
                @endif
                @if($errors->any())
                    {{implode(" ", $errors->all(':message'))}}
                @endif
            </div>
            <a href="/" class="logo">Home</a>
            <div class="profile">
                @auth
                    <span class="account_name">{{ auth()->user()->name }}</span>
                    <span class="balance" data-url="{{ route('getBalance') }}">{{ auth()->user()->balance }}₴</span>
                    <a href="{{ route('items') }}"><button class="button items">Items</button></a>
                    <a href="{{ route('logout') }}"><button class="button log_out">Log Out</button></a>
                @else
                    <a href="{{ route('login') }}"><button class="button log_in">Log In</button></a>
                    <a href="{{ route('register') }}"><button class="button register">Register</button></a>
                @endauth
            </div>
        </div>
        <div class="background-wrapper">
            <h2>Dota 2 Loot Boxes</h2>
            <span>Play and win unlimited amount of items. Beg for your luck</span>
        </div>
    </header>
    <div class="main">
        @yield('content')
    </div>
</body>
<script
  src="https://code.jquery.com/jquery-3.6.0.min.js"
  integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4="
  crossorigin="anonymous"></script>
<script src="/app.js"></script>