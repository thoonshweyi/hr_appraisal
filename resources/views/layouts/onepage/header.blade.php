<!DOCTYPE html>
<html>
    <head>
        <!-- Application Name -->
        <title>{{ config('app.name') }}</title>

        <meta charseet="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}"/>

        <!-- fav icon -->
        <link href="{{ asset('images/logo.png') }}" rel="icon" type="image/png" sizes="16x16"/>
        <!-- bootstrap css1 js1 -->
        {{-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-GLhlTQ8iRABdZLl6O3oVMWSktQOp6b7In1Zl3/Jr59b6EGGoI1aFkw7cmDA6j6gD" crossorigin="anonymous"> --}}
        <link href="{{ asset('assets/libs/bootstrap-5.3.3/bootstrap.min.css') }}" rel="stylesheet" >
        {{-- @vite(["resources/css/app.css","resources/js/app.js","public/assets/dist/css/style.css"]) --}}

        <!-- fontawesome css1 -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-1ycn6IcaQQ40/MKBW2W4Rhis/DbILU74C1vSrLJxCq57o941Ym01SwNsOMqvEBFlcgUa6xLiPY/NS5R+E6ztJQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <!-- jqueryui css1 js1 -->
        {{-- <link href="{{asset('./assets/libs/jquery-ui-1.13.2.custom/jquery-ui.min.css')}}" rel="stylesheet" type="text/css"> --}}

        <!-- toastr css1 js1 -->
        {{-- <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet" /> --}}

        <!-- custom css css1 -->
        <link href="{{ asset('assets/dist/css/style.css') }}" rel="stylesheet" type="text/css"/>

        <!-- Extra CSS -->
        @yield('css')

        {{-- pusher js1  --}}
        {{-- <script src="https://js.pusher.com/8.2.0/pusher.min.js"></script> --}}

    </head>
    <body>
