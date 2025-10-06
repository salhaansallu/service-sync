@extends('pos.app')
@section('dashboard')
<div id="hrComp">
    <hr-componenet v-bind:isadmin="@json(isAdmin())" v-bind:loadby="'{{ isset($_GET['loadby'])? sanitize($_GET['loadby']) : '' }}'" />
</div>
@endsection
