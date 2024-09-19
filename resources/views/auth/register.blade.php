@extends('layouts.app')

@section('title', 'Login')

@section('content')

    <div class="section mb-5 p-2 ">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 col-md-8 col-12">
                <form class="mt-3" method="POST" action="{{ route('register') }}">
                    @csrf

                    <div class="card">
                        <div class="card-body pb-1">

                            <div class="form-group basic">
                                <div class="input-wrapper">

                                    @if($errors->has('message'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('message') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <label class="label" for="token">Token</label>
                                    <input type="text" name="token" value="{{  old('token') }}" required autofocus
                                           autocomplete="token" class="form-control" id="token" placeholder="Token">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="form-group basic">
                                <div class="input-wrapper">

                                    @if($errors->has('name'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('name') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <label class="label" for="name">Name</label>
                                    <input type="text" name="name" value="{{  old('name') }}" required
                                           autocomplete="name" class="form-control" id="name" placeholder="Name">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="form-group basic">
                                <div class="input-wrapper">

                                    @if($errors->has('email'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('email') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

                                    <label class="label" for="email">E-mail</label>
                                    <input type="email" name="email" value="{{  old('email') }}" required
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
                                           required autocomplete="new-password" placeholder="Password">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="form-group basic">
                                <div class="input-wrapper">
                                    <label class="label" for="password_confirmation">Confirm Password</label>
                                    <input class="form-control" type="password" name="password_confirmation"
                                           required autocomplete="new-password" placeholder="Confirm Password">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-links mt-2">
                        <div>
                            <a href="{{ route('login') }}" class="text-muted d-flex align-items-center">
                                <ion-icon name="arrow-back-circle-outline" class="fs-6"></ion-icon> <span class="ms-1">Already registered?</span>
                            </a>
                        </div>
                    </div>

                    <hr>
                    <div class="transparent">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Register</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
