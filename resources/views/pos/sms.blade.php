@extends('pos.app')

@section('dashboard')
    <div class="content-page" id="dashboardSMS">
        <send-sms v-bind:customers="{{ $customers }}" />
    </div>
@endsection
