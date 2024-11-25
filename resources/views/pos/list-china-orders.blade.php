@extends('pos.app')
@section('dashboard')
<div id="ChinaOrders">
    <china-orders v-bind:customers="{{$customers}}" />
</div>
@endsection
