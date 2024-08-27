<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section my-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-12">
            <div class="card">
                <div class="card-body" id="client">

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>

    function clientController(response){

        if (response){
            console.log(response)

            let fields = ``

            for (let field of response.missing_data){

                if(field.name === "zip_code"){

                    fields += `
                    <br>
                    <div class="custom-alert my-2"></div>
                    `;

                }

                if(field.name === "document"){
                    fields += `

                    <div class="d-flex justify-content-start">
                        <div class="text-center form-check me-3">
                            <input class="form-check-input" type="radio" name="document-type" id="radioCpf" value="cpf">
                            <label class="form-check-label" for="radioCpf">${locale.label_pf}</label>
                        </div>
                        <div class="text-center form-check">
                            <input class="form-check-input" type="radio" name="document-type" id="radioCnpj" value="cnpj">
                            <label class="form-check-label" for="radioCnpj">${locale.label_pj}</label>
                        </div>
                    </div>


                    <div class="form-group basic">
                        <div class="sub-custom-alert my-2"></div>
                        <div class="input-wrapper">
                            <input type="${field.type}" class="form-control" autocomplete="n-password" id="${field.name}" name="${field.name}" placeholder="${field.label}">
                            <i class="clear-input">
                                <ion-icon name="close-circle"></ion-icon>
                            </i>
                        </div>
                    </div>

                    `
                    continue
                }

                fields += `
                    <div class="form-group basic">
                        <div class="input-wrapper">
                            <label class="label" for="${field.name}">
                                ${field.required ? `<ion-icon name="alert-circle-outline"></ion-icon>` : ""}
                                ${field.label}
                            </label>
                            <input type="${field.type}" class="form-control" autocomplete="n-password" id="${field.name}" name="${field.name}" placeholder="${field.label}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                        </div>
                    </div>
                `;

            }

            let formAttr = ""

            if (response.id){
                formAttr = `data-method="PUT" data-action="/client/${response.id}/"`
            }else{
                formAttr = `data-method="POST" data-action="/client/new/"`
            }

            $(`#client`).html(`
                <form ${formAttr} data-ajax="default" data-callback="">
                    ${fields}
                    <div class="row mt-2">
                        <div class="col-12">
                            <button type="submit" class="btn btn-lg btn-primary btn-block btn-submit">
                                ${response.id ? locale.label_btn_save : locale.create_client}
                            </button>
                        </div>
                    </div>
                </form>
            `)

            for (let field of response.missing_data) {
                let input_name = `#${field.name}`;

                if (field.name === "zip_code") {
                    $(input_name).mask("00000-000").on("keyup", function () {
                        get_cep(this.value);
                    });
                } else if (field.name === "document") {

                    $('input[name="document-type"]').on('change', function () {

                        let el = this;
                        if (el.checked) {
                            $(input_name).unmask();
                            $(input_name).mask(field.mask[el.value]).prop('placeholder', field.mask[el.value]).prop('required', !!field.required);
                        }

                        function handleKeyUp(event) {
                            get_cnpj(event.target.value)
                        }

                        if(el.value === 'cnpj'){
                            $('input[name="name"]').prop('required', false).parent().parent().hide();
                            $('input[name="company_name"]').prop('required', true).parent().parent().show();
                            $('input[name="trading_name"]').prop('required', false).parent().parent().show();
                            $(input_name).on('keyup', handleKeyUp)
                        }else{
                            auto_remove_alert(0)
                            $('input[name="name"]').prop('required', true).parent().parent().show();
                            $('input[name="company_name"]').prop('required', false).parent().parent().hide();
                            $('input[name="trading_name"]').prop('required', false).parent().parent().hide();
                            $(input_name).off('keyup', handleKeyUp)
                        }
                    });
                    $(input_name).prop('required', !!field.required)

                    if(field.value){
                        if(field.value.length === 18){
                            $('input[name="document-type"][value="cnpj"]').prop('checked', true).trigger('change');
                            $('input[name="company_name"]').prop('required', true).parent().parent().show();
                            $('input[name="trading_name"]').prop('required', false).parent().parent().show();
                            $('input[name="name"]').prop('required', false).parent().parent().hide();

                        }

                        if(field.value.length === 14){
                            $('input[name="document-type"][value="cpf"]').prop('checked', true).trigger('change');
                            $('input[name="company_name"]').prop('required', false).parent().parent().hide();
                            $('input[name="trading_name"]').prop('required', false).parent().parent().hide();
                            $('input[name="name"]').prop('required', true).parent().parent().show();
                        }
                        $(input_name).val(field.value).trigger('change');
                    }

                    continue;
                } else {

                    if(field.mask){
                        $(input_name).mask(field.mask).prop('required', !!field.required);
                    }else{
                        $(input_name).prop('required', !!field.required);
                    }

                }

                $(input_name).val(field.value).trigger('change');
            }

            $('.btn-submit').on('click', function(){
                $('input[required]').each(function() {
                    if (!this.checkValidity()) {
                        $(this).addClass('border-bottom-1 border-danger');
                    } else {
                        $(this).removeClass('border-bottom-1 border-danger');
                    }
                    // Força a exibição das mensagens de validação HTML5
                    this.reportValidity();
                });
            })

            $("form").on('submit', function (e){

                let form = $(this);
                const ajaxMod = $(form).data('ajax')
                if (ajaxMod === "default"){

                    e.preventDefault();
                    processForm(form);

                }
            })
        }

    }

    $(document).ready(() => {
        clientController(<?=$functionController->parseObjectToJson($client)?>)
    })
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
