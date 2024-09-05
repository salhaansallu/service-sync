@extends('layouts.app')

@section('content')
    <style>
        .addvert {
            display: none;
        }

        footer {
            padding-top: 100px
        }

        body {
            background-color: #f3f3f3;
        }
    </style>

<div id="register_info">
    <register-info v-bind:app_url="'{{ env('app.url') }}'" />
</div>
@endsection
