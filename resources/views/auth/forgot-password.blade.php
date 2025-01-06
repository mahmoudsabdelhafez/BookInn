@extends('frontend.main_master')
@section('main')
    <!-- Inner Banner -->
    <div class="inner-banner inner-bg9">
        <div class="container">
            <div class="inner-title">
                <ul>
                    <li>
                        <a href="index.html">Home</a>
                    </li>
                    <li><i class='bx bx-chevron-right'></i></li>
                    <li>Forgot Password</li>
                </ul>
                <h3>Forgot Your Password?</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Forgot Password Area -->
    <div class="sign-in-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="user-all-form">
                        <div class="contact-form">
                            <div class="section-title text-center">
                                <span class="sp-color">Forgot Password</span>
                                <h2>Reset Your Password!</h2>
                                <p class="text-sm text-gray-600">
                                    {{ __('Forgot your password? No problem. Just let us know your email address and we will email you a password reset link that will allow you to choose a new one.') }}
                                </p>
                            </div>

                            <!-- Session Status -->
                            <x-auth-session-status class="mb-4" :status="session('status')" />

                            <form method="POST" action="{{ route('password.email') }}">
                                @csrf

                                <div class="row">
                                    <!-- Email Address -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input type="email" name="email" id="email" class="form-control" 
                                                value="{{ old('email') }}" required autofocus placeholder="Enter your Email">
                                        </div>
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-lg-12 text-center">
                                        <button type="submit" class="default-btn btn-bg-three border-radius-5">
                                            {{ __('Email Password Reset Link') }}
                                        </button>
                                    </div>

                                    <div class="col-12 text-center">
                                        <p class="account-desc">
                                            Remembered your password?
                                            <a href="{{ route('login') }}">Sign In</a>
                                        </p>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Forgot Password Area End -->
@endsection
