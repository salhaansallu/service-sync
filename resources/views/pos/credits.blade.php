@extends('pos.app')
@section('dashboard')
<div id="dashboardCredits">
    <dashboard-credits v-bind:credits='{{$credits}}' v-bind:customers='{{$customers}}' v-bind:summary='{{$ageingSummary}}' v-bind:isadmin='@json(isAdmin())' />
</div>
@endsection
