@extends('layouts.app')

@section('content')
    <style>
        .navigation,
        .addvert,
        footer {
            display: none;
        }
        .login_form .brand img {
            width: 100px !important;
        }
    </style>

    <div class="auth-wrap" style="background-image: url('{{ asset('assets/images/auth/auth_bg.png') }}');">
        <div class="login_form">
            <div class="brand">
                <img src="{{ asset('assets/images/brand/logo.png') }}" alt="">
            </div>

            <div class="heading mb-4">
                <h3>Partner Portal Login</h3>
            </div>

            {{-- <div class="new_register">
                Don't have an account? <a href="/signup">Register now</a>
            </div> --}}

            <form method="POST" action="{{ route('partnerSignin') }}">
                @csrf
                @isset($error)
                    <span class="invalid-feedback mb-3 d-block" role="alert">
                        <strong>{{ $error['message'] }}</strong>
                    </span>
                @endisset
                <div class="input">
                    <div class="label">Username/Email address</div>
                    <input type="text" placeholder="Username/Email address"
                        class="@isset($error) form-control is-invalid @endisset" name="email"
                        value="@isset($error['email']){{ $error['email'] }}@endisset" required autocomplete="email" autofocus>
                </div>
                <div class="input">
                    <div class="label">Password</div>
                    <input type="password" placeholder="Password" name="password"
                        class="@isset($error) form-control is-invalid @endisset" required
                        autocomplete="current-password">
                </div>

                {{-- <div class="remember">
                    <input type="checkbox" name="remember" id="remember">
                    <span>Remember me</span>
                </div> --}}

                <button type="submit" class="primary-btn">Log in</button>

                {{-- <div class="new_register text-start">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div> --}}

            </form>

            <div class="copyrights mt-5">
                Copyright &copy; {{ date('Y') }} We Fix TV Panel Repair. <br>All Rights Received.
            </div>
        </div>
    </div>

    {{-- @error('email')
        <script>
            //window.toastr.error("Error", "{{ $message }}");
            console.log(window.toastr);
        </script>
    @enderror

    @error('password')
        <script>
            window.toastr.error("Error", "{{ $message }}");
        </script>
    @enderror --}}

    {{-- <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="row mb-3">
                                <label for="email"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Email Address') }}</label>

                                <div class="col-md-6">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <label for="password"
                                    class="col-md-4 col-form-label text-md-end">{{ __('Password') }}</label>

                                <div class="col-md-6">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="row mb-3">
                                <div class="col-md-6 offset-md-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div> --}}
@endsection
