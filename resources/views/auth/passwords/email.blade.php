@extends('layouts.app')

@section('content')
<style>
    .navigation,
    .addvert,
    footer {
        display: none;
    }
</style>


<div class="auth-wrap" style="background-image: url('{{ asset('assets/images/auth/auth_bg.png') }}');">
    <div class="login_form">
        <div class="brand">
            <img src="{{ asset('assets/images/brand/logo.png') }}" alt="">
        </div>

        <div class="heading">
            <h3>Reset Password</h3>
        </div>

        <form method="POST" class="mt-4" action="{{ route('password.email') }}">
            @csrf

            <div class="input">
                <div class="label">Email address</div>
                <input type="email" placeholder="Email address"
                    class="form-control @error('email') is-invalid @enderror" name="email"
                    value="{{ old('email') }}" required autocomplete="email" autofocus>
            </div>

            <button type="submit" class="primary-btn">Send Reset Link</button>

            <div class="new_register text-start">
                <a href="{{ route('userLogin') }}">Back to login</a>
            </div>

        </form>

        <div class="copyrights">
            Copyright &copy; {{ date('Y') }} NMSware Technologies. <br>All Rights Received
        </div>
    </div>
</div>


{{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Reset Password') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Send Password Reset Link') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div> --}}
@endsection
