@extends('pos.app')
@section('dashboard')
<div id="hrComp">
    <hr-componenet v-bind:isadmin="@json(isAdmin())" />
</div>
@endsection
