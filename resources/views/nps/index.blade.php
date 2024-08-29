@extends('layouts.app')

@section('title', 'miniNPS')

@section('content')

    <div class="section text-center">
        <img src="{{ asset('assets/img/logo.png') }}" alt="">
        <h4 class="mt-2">Sua Opinião é Importante!</h4>
    </div>

    <div class="section p-2">

        <div class="row d-flex justify-content-center">
            <div class="col-lg-4 col-md-8 col-12">
                <form class="mt-3" method="POST" action="{{ route('nps.store') }}">
                    @csrf

                    <div class="card">
                        <div class="card-body">

                            @foreach($nps as $value)

                                @if($value->isVisible())

                                <div class="pt-2 pb-2">

                                    {{ $value->question }}
                                    <br>

                                    @php
                                    $attrs = $value->getValuesAttribute()
                                    @endphp

                                    <input type="hidden" name="answer"/>
                                    <input type="hidden" name="question" value="{{ $value->id }}"/>
                                    <input type="hidden" name="page" value="{{ $page }}"/>

                                    @switch($value->range)
                                        @case('minimal')

                                            <div class="row">
                                                @foreach($attrs as $key => $val)
                                                    <button class="col m-1 mb-1 btn btn-lg border border-1 btn-action" data-action="select" type="button" name="button-{{ $value->id }}" value="{{ $val }}">
                                                        {{ $val }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            @break
                                        @case('emoji')
                                            <div class="row">
                                                @foreach($attrs as $key => $val)
                                                    <button class="col m-1 mb-1 btn btn-lg border border-1 btn-action" data-action="select" type="button" name="button-{{ $value->id }}" value="{{ $val }}">
                                                        {{ $key }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            @break
                                        @case('default')
                                            <div class="row">
                                                @foreach($attrs as $key => $val)
                                                    <button class="col m-1 mb-1 btn btn-lg border border-1 btn-action" data-action="select" type="button" name="button-{{ $value->id }}" value="{{ $val }}">
                                                        {{ $key }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            @break
                                        @default()
                                            <div class="form-group boxed">
                                                <div class="input-wrapper">
                                                    <label class="label" for="question-{{ $value->id }}">Descreva no campo abaixo (opcional):</label>
                                                    <textarea id="question-{{ $value->id }}"  name="question-{{ $value->id }}" rows="4" class="form-control" placeholder="Descrição"></textarea>
                                                    <i class="clear-input">
                                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                                    </i>
                                                </div>
                                            </div>

                                            <br>
                                            <div class="mb-05">
                                                Gostaríamos de acompanhar sua avaliação de perto. Se possível, deixe seu nome, e-mail ou
                                                telefone para que possamos entrar em contato e garantir que sua experiência seja ainda melhor.
                                                Sua opinião é valiosa, e estamos aqui para oferecer o suporte que você precisa.
                                            </div>
                                            <div class="form-group basic animated">
                                                <div class="input-wrapper">
                                                    <label class="label" for="name">*Seu nome e sobrenome</label>
                                                    <input type="text" class="form-control" id="name" name="name" placeholder="*Seu nome e sobrenome" required>
                                                    <i class="clear-input">
                                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                                    </i>
                                                </div>
                                            </div>

                                            <div class="form-group basic animated">
                                                <div class="input-wrapper">
                                                    <label class="label" for="phone">Seu telefone - (00) 00000-0000</label>
                                                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Seu telefone - (00) 00000-0000">
                                                    <i class="clear-input">
                                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                                    </i>
                                                </div>
                                            </div>

                                            <div class="form-group basic animated">
                                                <div class="input-wrapper">
                                                    <label class="label" for="email">E-mail</label>
                                                    <input type="email" class="form-control" id="email" name="email" placeholder="E-mail">
                                                    <i class="clear-input">
                                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                                    </i>
                                                </div>
                                            </div>
                                            @break
                                    @endswitch
                                </div>
                                @endif
                            @endforeach
                        </div>
                    </div>

                    <br>
                    <div class="transparent">
                        <button type="submit" class="btn btn-primary btn-block btn-lg">Enviar</button>
                    </div>

                </form>
            </div>
        </div>

    </div>

    <script>
        $(document).ready(function(){

            $(".btn-action").on('click', function (){
                let btn = $(this)
                let input = $('input[name="answer"]')

                // Remove a classe 'btn-success' de todos os botões com a classe 'btn-action'
                $(".btn-action").removeClass('btn-success').prop('data-action', 'select');

                // Adiciona a classe 'btn-success' ao botão clicado e define a ação como 'unselect'
                btn.addClass('btn-success').prop('data-action', 'unselect');

                // Define o valor do input 'answer' com o valor do botão clicado
                input.val(btn.val()).change();
            });


            $("form").on('submit', function (e){
                if(!$('input[name="answer"]').val()){
                    e.preventDefault();
                    dialog({
                        message: `É obrigatório selecionar uma das opções!`,
                        status: `warning`
                    })
                }
            })

            $("#phone").mask("(00) 00000-0000")
        })
    </script>

@endsection
