@extends('layouts.app')

@section('title', 'NPS')

@section('content')

    <div class="section mt-2">
        <div class="section-title">
            <span>Pesquisas</span>
        </div>

        <div class="row">
            <div class="col-md-4 col-12 mt-2">
                <div class="card">
                    <div class="table-responsive p-2 pb-0">
                        @if(session('msg'))
                            <div class="alert alert-primary alert-dismissible fade show mb-2" role="alert">
                                {{ session('msg') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="form-group basic">
                            <div class="input-wrapper">
                                <label class="label" for="question_type">Tipo de pergunta padrão</label>
                                <select class="form-control custom-select" name="question_type" id="question_type" required>
                                    <optgroup label="Primeira etapa">
                                        <option value="emoji" selected>Emoji</option>
                                        <option value="default">Notas (0 a 10)</option>
                                        <option value="minimal">Notas (1 a 5)</option>
                                    </optgroup>
                                    <optgroup label="Segunda etapa">
                                        <option value="why">Por que?</option>
                                    </optgroup>
                                </select>
                            </div>
                            <div class="input-info">Selecione um tipo de pergunta padrao que sera exibida aos seus avaliadores</div>

                            <div class="text-warning w-100 mt-3">
                                <div class="text-center">
                                    !! ----------- Observação ----------- !!
                                </div>
                                Na primeira etapa, só é possível selecionar apenas uma (1) opção entre
                                os três (3) tipos de perguntas. Isso significa que, ao ativar uma pergunta do tipo
                                Emoji, todas as outras perguntas, incluindo as de outros tipos
                                (como as de Notas de (1 a 5) ou (1 a 10)), serão automaticamente desativadas.
                            </div>

                            <br>
                            <h4>Solicitar Campos:</h4>
                            <ul class="listview simple-listview transparent card border px-0">
                                <li>
                                    <div>Nome</div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="guest-name" type="checkbox" id="guest-name" checked>
                                        <label class="form-check-label" for="guest-name"></label>
                                    </div>
                                </li>
                                <li>
                                    <div>E-mail</div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="guest-email" type="checkbox" id="guest-email" checked>
                                        <label class="form-check-label" for="guest-email"></label>
                                    </div>
                                </li>
                                <li>
                                    <div>Telefone</div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" name="guest-phone" type="checkbox" id="guest-phone" checked>
                                        <label class="form-check-label" for="guest-phone"></label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-8 col-12 mt-2">
                <div id="questions"></div>
            </div>
            <div class="col-md-8 col-12 mt-2">
                <div id="questions"></div>
            </div>
        </div>
    </div>

    <script>

        const arrayNps = @json($array_nps, JSON_UNESCAPED_UNICODE);

        $(document).ready(() => {

            const question_type = $("#question_type");
            question_type.on('change', function(){

                $("#questions").html(`
                    <div class="card accordion" id="accordionQuestions">
                    </div>
                `)

                for (let value of arrayNps[this.value]){
                    console.log(value)
                    $('#accordionQuestions').append(`
                        <div class="accordion-item">
                            <h2 class="accordion-header">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-${value.id}" aria-expanded="false">
                                    <ion-icon name="help-outline" role="img" class="md hydrated" aria-label=""></ion-icon>
                                    ${value.visibility ? `
                                        <ion-icon class="text-success" name="checkmark-circle-outline"></ion-icon>
                                    ` : `
                                        <ion-icon class="text-danger" name="ban-outline"></ion-icon>
                                    `} ${value.question}
                                </button>
                            </h2>
                            <div id="accordion-${value.id}" class="accordion-collapse collapse" data-bs-parent="#accordionQuestions" style="">
                                <div class="accordion-body border">
                                    <form>
                                        <div class="card border p-2 mb-2">
                                            <div class="form-group basic">
                                                <div class="input-wrapper">
                                                    <label class="label" for="question-${value.id}">Título da Pesquisa</label>
                                                    <input type="text" class="form-control" id="question-${value.id}" name="question-${value.id}" placeholder="Título da Pesquisa" required value="${value.question}">
                                                    <i class="clear-input">
                                                        <ion-icon name="close-circle" role="img" class="md hydrated" aria-label="close circle"></ion-icon>
                                                    </i>
                                                </div>
                                            </div>
                                        </div>
                                        <ul class="card listview simple-listview transparent flush border">
                                            <li>
                                                <div>Status</div>
                                                <div class="form-check form-switch">
                                                    <input class="form-check-input" name="active-${value.id}" type="checkbox" id="active-${value.id}" ${value.visibility ? `checked` : ""}>
                                                    <label class="form-check-label" for="active-${value.id}"></label>
                                                </div>
                                            </li>
                                        </ul>
                                    </form>
                                </div>
                            </div>
                        </div>
                    `)

                }
            })

            console.log(arrayNps)

            for (let [category, questions] of Object.entries(arrayNps)) {
                for (let question of questions) {
                    if (question.visibility === "default" && question.range !== "why") {
                        question_type.val(question.range).trigger('change');
                        break; // Caso queira apenas ativar uma pergunta, interrompa o loop após a primeira alteração
                    }
                }
            }

        })
    </script>
@endsection
