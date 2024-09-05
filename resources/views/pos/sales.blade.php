@extends('pos.app')
@section('dashboard')
<div id="dashboardSales">
    <dashboard-sales v-bind:bladesales='{{$sales}}' v-bind:customers="{{ $customers }}" v-bind:cahiers="{{ $cahiers }}" />
</div>
@endsection
