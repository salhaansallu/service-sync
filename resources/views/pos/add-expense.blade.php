@extends('pos.app')

@section('dashboard')
<div class="content-page">
    <div class="container-fluid add-form-list">
        @include('pos.partials.add-expense')
    </div>
</div>
@endsection
