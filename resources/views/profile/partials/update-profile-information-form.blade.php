
<div class="section mt-2">
    <div class="card">
        <div class="card-header">
            {{ __('Profile Information') }}
        </div>
        <div class="card-body">

            <p class="mt-1 text-sm">
                {{ __("Update your account's profile information and email address.") }}
            </p>

            <form id="send-verification" method="post" action="{{ route('verification.send') }}">
                @csrf
            </form>

            <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
                @csrf
                @method('patch')

                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        @if (session('status') === 'profile-updated')
                            <div class="alert alert-success alert-dismissible fade show mb-2" role="alert">
                                {{ __('Saved.') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="name">{{ __('Name') }}</label>
                                <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" placeholder="Name" required autofocus autocomplete="name">
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                            @if($errors->has('name'))
                                <div class="input-info text-danger"> {{ $errors->first('name') }} </div>
                            @endif
                        </div>
                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="email">{{ __('Email') }}</label>
                                <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" placeholder="Email" required autofocus autocomplete="username">
                                <i class="clear-input">
                                    <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                </i>
                            </div>
                            @if($errors->has('email'))
                                <div class="input-info text-danger"> {{ $errors->first('email') }} </div>
                            @endif

                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div>
                                    <p class="text-sm mt-2 text-gray-800">
                                        {{ __('Your email address is unverified.') }}

                                        <button form="send-verification" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                            {{ __('Click here to re-send the verification email.') }}
                                        </button>
                                    </p>

                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-2 font-medium text-sm text-green-600">
                                            {{ __('A new verification link has been sent to your email address.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif
                        </div>

                    </div>
                </div>
                <br>
                <div class="row">
                    <div class=" col-lg-2 col-md-3 col-6">
                        <button class="btn btn-primary btn-block btn-lg" type="submit">
                            {{ __('Save') }}
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>
