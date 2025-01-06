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
                    <li>Reset Password</li>
                </ul>
                <h3>Reset Your Password</h3>
            </div>
        </div>
    </div>
    <!-- Inner Banner End -->

    <!-- Reset Password Area -->
    <div class="sign-in-area pt-100 pb-70">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="user-all-form">
                        <div class="contact-form">
                            <div class="section-title text-center">
                                <span class="sp-color">Reset Password</span>
                                <h2>Enter Your New Password</h2>
                            </div>

                            <form method="POST" action="{{ route('password.store') }}">
                                @csrf

                                <!-- Password Reset Token -->
                                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                                <div class="row">
                                    <!-- Email Address -->
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <input  type="email" name="email" id="email" class="form-control" 
                                                value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="Enter your Email">
                                        </div>
                                        @if ($errors->has('email'))
                                            <span class="text-danger">{{ $errors->first('email') }}</span>
                                        @endif
                                    </div>

                                    <!-- Password -->
                                    <div class="col-lg-12 mt-4">
                                        <div class="form-group">
                                            <input type="password" name="password" id="password" class="form-control" required autocomplete="new-password" placeholder="Enter New Password">
                                        </div>
                                        @if ($errors->has('password'))
                                            <span class="text-danger">{{ $errors->first('password') }}</span>
                                        @endif
                                    </div>

                                    <!-- Confirm Password -->
                                    <div class="col-lg-12 mt-4">
                                        <div class="form-group">
                                            <input type="password" name="password_confirmation" id="password_confirmation" class="form-control" required autocomplete="new-password" placeholder="Confirm New Password">
                                        </div>
                                        @if ($errors->has('password_confirmation'))
                                            <span class="text-danger">{{ $errors->first('password_confirmation') }}</span>
                                        @endif
                                    </div>

                                    <div class="col-lg-12 text-center mt-4">
                                        <button type="submit" class="default-btn btn-bg-three border-radius-5">
                                            Reset Password
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Reset Password Area End -->
@endsection
