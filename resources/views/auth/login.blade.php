@extends('layouts.app')

@section('title', 'Login')

@section('content')

    <div class="section mb-5 p-2 ">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 col-md-8 col-12">
                <form class="mt-3" method="POST" action="{{ route('login') }}">
                    @csrf

                    <div class="card">
                        <div class="card-body pb-1">
                            <div class="form-group basic">
                                <div class="input-wrapper">

                                    @if($errors->has('email'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('email') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <label class="label" for="email">E-mail</label>
                                    <input type="email" name="email" value="{{  old('email') }}" required autofocus
                                           autocomplete="username" class="form-control" id="email" placeholder="Your e-mail">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="form-group basic">
                                <div class="input-wrapper">
                                    <label class="label" for="password">Password</label>
                                    <input class="form-control" type="password" name="password"
                                           required autocomplete="current-password" placeholder="Your password">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-links mt-2">
                        <div>
                            <a href="{{ route('register') }}" class="d-flex align-items-center">
                                <ion-icon name="person-add-outline" class="fs-6"></ion-icon> <span class="ms-1">Register Now</span>
                            </a>
                        </div>
                        <div>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="text-muted">Forgot Password?</a>
                            @endif
                        </div>
                    </div>

                    <hr>
                    <div class="transparent">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Log in</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
