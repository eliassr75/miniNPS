@extends('layouts.app')

@section('title', 'miniNPS')

@section('content')

    <div class="section text-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="">
    </div>

    <div class="section mt-2 p-2">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 col-md-8 col-12">

                <div class="card">
                    <div class="card-body text-center">
                        <ion-icon name="checkmark-circle-outline" class="text-success my-2" style="font-size: 55px"></ion-icon>
                        <br>
                        <p>
                            Obrigado pelo seu feedback!
                        </p>
                    </div>
                </div>

            </div>
        </div>

    </div>

@endsection
