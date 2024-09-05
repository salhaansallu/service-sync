@extends('pos.app')
@section('dashboard')
<div id="dashboardCredits">
    <dashboard-credits v-bind:credits='{{$credits}}' v-bind:customers='{{$customers}}' />
</div>
@endsection
