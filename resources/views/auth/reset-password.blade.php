@extends('layouts.app')

@section('title', 'Profile')

@section('content')

    <div class="section mb-5 p-2 ">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 col-md-8 col-12">
                <form class="mt-3" method="POST" action="{{ route('password.store') }}">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                                    <input type="email" name="email" value="{{  old('email', $request->email) }}" required autofocus
                                           autocomplete="username" class="form-control" id="email" placeholder="Your e-mail">
                                    <i class="clear-input">
                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="form-group basic">
                                <div class="input-wrapper">

                                    @if($errors->has('password'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('password') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

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

                                    @if($errors->has('password_confirmation'))
                                        <div class="alert alert-danger alert-dismissible fade show mb-2" role="alert">
                                            {{ $errors->first('password_confirmation') }}
                                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                        </div>
                                    @endif

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

                    <br>
                    <div class="transparent">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Reset Password</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

@endsection
