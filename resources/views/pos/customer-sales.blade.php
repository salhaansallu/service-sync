@extends('pos.app')
@section('dashboard')
<div id="customerSales">
    <customer-sales v-bind:customers="{{ $customers }}" v-bind:cahiers="{{ $cahiers }}" />
</div>
@endsection
