@extends('pos.app')
@section('dashboard')
<div id="dashboardPartnerCredits">
    <dashboard-partner-credits v-bind:credits='{{$credits}}' v-bind:partners='{{$partners}}' />
</div>
@endsection
