@extends('pos.app')
@section('dashboard')
<div id="hrComp">
    <hr-componenet v-bind:isadmin="{{ isAdmin() }}" />
</div>
@endsection
