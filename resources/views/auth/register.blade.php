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
        <div class="login_form register-wrap">
            <div class="brand">
                <img src="{{ asset('assets/images/brand/logo.png') }}" alt="">
            </div>

            <div class="heading">
                <h3>Register</h3>
            </div>

            <div class="new_register">
                Already have an account? <a href="/signin">Login now</a>
            </div>

            <form method="post" action="{{ route('signup') }}" autocomplete="off">
                @isset($error)
                    <span class="invalid-feedback mb-3 d-block" role="alert">
                        <strong>{{ $error['msg'] }}</strong>
                    </span>
                @endisset
                @csrf
                <div class="input">
                    <div class="label">Name</div>
                    <input type="text" placeholder="Name" name="name" value="@isset($name) {{ $name }} @endisset">
                </div>
                <div class="input">
                    <div class="label">Email</div>
                    <input type="email" placeholder="Email" name="email" value="@isset($email) {{ $email }} @endisset">
                </div>

                <div class="input">
                    <div class="label">Password</div>
                    <input type="password" placeholder="Password" name="password">
                </div>

                {{-- <div class="remember">
                    <input type="checkbox" name="" id=""> <span>Remember me</span>
                </div> --}}

                <button type="submit" class="primary-btn">Register</button>

            </form>

            <div class="copyrights mt-5">
                Copyright &copy; {{ date('Y') }} NMSware Technologies. <br>All Rights Received
            </div>
        </div>
    </div>

    {{-- <div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Register') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf

                        <div class="row mb-3">
                            <label for="name" class="col-md-4 col-form-label text-md-end">{{ __('Name') }}</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>

                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label for="password-confirm" class="col-md-4 col-form-label text-md-end">{{ __('Confirm Password') }}</label>

                            <div class="col-md-6">
                                <input id="password-confirm" type="password" class="form-control" name="password_confirmation" required autocomplete="new-password">
                            </div>
                        </div>

                        <div class="row mb-0">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Register') }}
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
