@extends('layouts.app')

@section('content')
    <style>
        body {
            background: #f3f3f3;
        }
    </style>

    <div class="container">
        <div class="account">
            <div id="nav_panel" class="nav_panel">
                <div class="profile">
                    <img src="{{ profileImage(userData()->profile) }}" alt="">
                </div>
                <div class="name">{{ Auth::user()->fname }}</div>
                <div class="email">{{ Auth::user()->email }}</div>
                <ul>
                    <li><a href="/account/overview" class="{{ Request::is('account/overview') ? 'active' : '' }}">Overview</a></li>
                    <li><a href="/account/details" class="{{ Request::is('account/details') ? 'active' : '' }}">Account details</a></li>
                    <li><a href="/account/logout">Logout</a></li>
                </ul>
            </div>

            <div class="details_pannel">
                @yield('account_content')
            </div>
        </div>
    </div>

    <script>
        $('#account_menu').click(function(e) {
            if ($('.nav_panel').hasClass('open')) {
                $('.nav_panel').removeClass('open')
            }else {
                $('.nav_panel').addClass('open')
            }
        });

    </script>
@endsection
