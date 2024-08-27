<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-12">
            <div class="card">
                <div id="users">

                    <!-- class="search" automagically makes an input a search field. -->
                    <div class="px-3 pt-3 d-flex">
                        <form class="search-form">
                            <div class="form-group searchbox">
                                <input type="text" class="form-control search">
                                <i class="input-icon">
                                    <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                                </i>
                            </div>
                        </form>
                        <a
                            <?php if (!isset($url)): ?>
                                href="javascript:void(0)" onclick="actionForm('<?=$actionForm?>')"
                                data-bs-toggle="modal" data-bs-target="#actionSheetForm"
                            <?php else: ?>
                                href="<?=$url?>"
                            <?php endif; ?>
                                class=" ms-2 btn btn-primary">
                            <ion-icon name="add-outline"></ion-icon>
                            <?=$functionController->locale('label_btn_add')?>
                        </a>
                    </div>
                    <!-- class="sort" automagically makes an element a sort buttons. The date-sort value decides what to sort by. -->
                    <!--
                    <button class="sort btn btn-primary" data-sort="name">
                        Sort
                    </button>
                    -->

                    <ul class="listview image-listview inset list my-2">
                        <?php foreach ($users as $user): ?>
                        <li id="li-model">
                            <a href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#actionSheetFormUser" onclick="userController(false, <?=$user->id?>)" class="item">
                                <img src="/assets/img/sample/avatar/do-utilizador.png" alt="image" class="image">
                                <div class="in">
                                    <div>
                                        <header class="permission"><?=$user->current_permission?></header>
                                        <span class="name"><?=$user->name?></span>
                                        <footer class="str_created">
                                            <?=$user->str_created?>
                                        </footer>
                                    </div>
                                </div>
                            </a>
                            <div class="form-check form-switch me-2">
                                <input class="form-check-input" type="checkbox" value="<?=$user->id?>" <?=$user->active ? "checked" : ""?> id="SwitchCheckUser<?=$user->id?>" onchange="change('/users/change/', <?=$user->id?>, this)">
                                <label class="form-check-label" for="SwitchCheckUser<?=$user->id?>"></label>
                            </div>
                        </li>
                        <?php endforeach; ?>
                    </ul>

                    <div class="modal fade modalbox" id="actionSheetFormUser" tabindex="-1" aria-hidden="true">
                        <div class="modal-dialog" role="document">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title"></h5>
                                    <a href="javascript:void(0);" data-bs-dismiss="modal" class="btn-close btn-close-white">
                                    </a>
                                </div>
                                <div class="modal-body">
                                    <div class="action-sheet-content" id="user-info">
                                        <div id="section-animation" class="w-100 d-flex justify-content-center"></div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <div id="child-global-custom-alert"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>

    function setLegend(permission_id){
        for (let permission of window.permissions) {
            if(permission.id == permission_id) {
                $('#legend-permission').html(permission.description)
            }
        }
    }

    function userController(response=false, id=false){

        if (id){

            $(".modal-title").html('')

            params = {
                method: "GET",
                url: `${window.location.pathname}json/${id}`,
                dataType: 'json',
            }
            processForm(false, params, "userController", false, true)
        }

        if (response){
            response = response.responseJSON;
            console.log(response)

            $(".modal-title").html(response.user.name)

            let fields = ``

            fields += `

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="name">${locale.input_name}</label>
                    <input type="text" class="form-control" id="name" name="name" value="${response.user.name}" placeholder="${locale.input_name}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="email">${locale.input_email}</label>
                    <input type="email" class="form-control" id="email" name="email" value="${response.user.email}" placeholder="${locale.input_email}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="username">
                        ${locale.input_username}
                        <span class="text-warning">
                            (${locale.warning_automaticale_generated})
                        </span>
                    </label>
                    <input type="text" class="form-control" id="username" name="username" value="${response.user.username}" placeholder="${locale.input_username}" readonly required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>

            `

            window.permissions = response.permissions
            let values_select = ""
            for (let permission of window.permissions){
                values_select += `
                    <option value="${permission.id}" ${permission.name == response.user.current_permission ? "selected" : ""}>${permission.name}</option>
                `
            }

            let values_select_language = ""
            let languages = [
                {language: "pt", label: locale.language_system_pt},
                {language: "en", label: locale.language_system_en},
                {language: "es", label: locale.language_system_es}
            ]

            for (let language of languages){
                values_select_language += `
                    <option value="${language.language}" ${language.language == response.user.language ? "selected" : ""}>${language.label}</option>
                `
            }

            fields += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-permission">${locale.permission_level}</label>
                    <select class="form-control custom-select" id="select-permission" name="permission" onchange="setLegend(this.value)" required>
                        <option value="" selected disabled>Selecione uma opção</option>
                        ${values_select}
                    </select>
                    <label class="text-warning label mt-1" id="legend-permission"></label>
                </div>
            </div>

            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-language">${locale.language_system}</label>
                    <select class="form-control custom-select" id="select-language" name="language" required>
                        <option value="" selected disabled>Selecione uma opção</option>
                        ${values_select_language}
                    </select>
                    <label class="text-warning label mt-1" id="legend-permission"></label>
                </div>
            </div>
            `;

            for (let field of response.user.missing_data){

                if(field.name === "zip_code"){

                    fields += `
                    <br>
                    <div class="custom-alert my-2"></div>
                    `;

                }

                fields += `
                    <div class="form-group basic">
                        <div class="input-wrapper">
                            <label class="label" for="${field.name}">${field.label}</label>
                            <input type="${field.type}" class="form-control" autocomplete="n-password" id="${field.name}" name="${field.name}" placeholder="${field.label}">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                        </div>
                    </div>
                `;

            }

            $(`#user-info`).html(`
                <form data-method="PUT" data-action="/users/${response.user.id}/" data-ajax="default" data-callback="">
                    ${fields}
                    <div class="row mt-2">
                        <div class="col-6">
                            <button type="button" class="btn btn-lg btn-cancel btn-outline-secondary btn-block" data-bs-dismiss="modal">
                                ${locale.label_btn_cancel}
                            </button>
                        </div>
                        <div class="col-6">
                            <button type="submit" class="btn btn-lg btn-primary btn-block btn-submit">
                                ${locale.label_btn_save}
                            </button>
                        </div>
                    </div>
                </form>
            `)

            for (let field of response.user.missing_data){

                let input = `#${field.name}`;

                if(field.name === "zip_code"){
                    $(input).mask("00000-000").on("keyup", function (){
                        get_cep(this.value)
                    })
                }else{
                    $(input).mask(`${field.mask}`).attr('required', (!!field.required))
                }

                $(input).val(field.value).trigger('change')

            }

            $('#select-permission, #name, #email, #username').trigger('change')

            $('.btn-close, .btn-cancel').on('click', () => {
                $(".modal-title").html('')
                $("#user-info").html('<div id="section-animation" class="w-100 d-flex justify-content-center"></div>')
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

    window.systemPermissions = <?=$functionController->parseObjectToJson($permissions)?>;
    $(document).ready(() => {
        let options = {
            valueNames: [ 'permission', 'name', 'str_created' ]
        };
        window.userList = new List('users', options);
    })
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
