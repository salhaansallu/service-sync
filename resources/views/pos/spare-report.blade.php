@extends('pos.app')
@section('dashboard')
<div id="SpareReport">
    <spare-report v-bind:reports='{{$reports}}' />
</div>
@endsection
