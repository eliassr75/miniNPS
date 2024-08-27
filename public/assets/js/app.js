const spinner = `<div class="loader"></div>`;
const spinner_upload = `<div class="loader-upload"></div>`;

let configDataTable = {
    stateSave: true,
    responsive: true,
    language: {
        searchPlaceholder: 'Faça uma pesquisa nesta página',
        zeroRecords: "Não encontramos resultados...",
        sSearch: '',
        sLengthMenu: '_MENU_',
        sLength: 'dataTables_length',
        info: 'Total de Registros: _TOTAL_',
        infoFiltered: '(Filtrado de _MAX_ resultados)',
        infoEmpty: "Total de Registros: _TOTAL_",
        oPaginate: {
            sFirst: '<ion-icon name="arrow-back-circle-outline"></ion-icon>',
            sPrevious: '<ion-icon name="arrow-back-circle-outline"></ion-icon>',
            sNext: '<ion-icon name="arrow-forward-circle-outline"></ion-icon>',
            sLast: '<ion-icon name="arrow-forward-circle-outline"></ion-icon>'
        }
    },
    order: false,
    lengthChange: false,
    autoWidth: false,
    paging: false
}

function scrollToBottom() {
    window.scrollTo({
        top: document.body.scrollHeight,
        behavior: 'auto'
    });
}

function updateLocalStorage(keyOrObject, newValue) {
    const storageKey = 'jsonStorage';
    let storedData = localStorage.getItem(storageKey);

    let jsonData = storedData ? JSON.parse(storedData) : {};

    if (typeof keyOrObject === 'object') {
        jsonData = { ...jsonData, ...keyOrObject };
    } else {
        jsonData[keyOrObject] = newValue;
    }

    localStorage.setItem(storageKey, JSON.stringify(jsonData));
}

function getLocalStorageData() {
    const storageKey = 'jsonStorage';
    let storedData = localStorage.getItem(storageKey);
    return storedData ? JSON.parse(storedData) : {};
}

function updateItemsOrderList(newItem) {
    const storageKey = 'itemsOrderList';

    let storedData = localStorage.getItem(storageKey);
    let itemsArray = storedData ? JSON.parse(storedData) : [];

    // Gerar um ID aleatório entre 10000 e 99999
    function generateRandomId() {
        return Math.floor(Math.random() * 90000) + 10000;
    }

    if (newItem.id) {
        // Se o item já tiver um ID, atualize o item existente
        let itemIndex = itemsArray.findIndex(item => item[newItem.id]);
        if (itemIndex !== -1) {
            itemsArray[itemIndex][newItem.id] = newItem;
        } else {
            itemsArray.push({ [newItem.id]: newItem });
        }
    } else {
        // Caso contrário, atribua um novo ID aleatório e adicione o item
        let newId;
        do {
            newId = generateRandomId();
        } while (itemsArray.some(item => item[newId])); // Garante que o ID seja único

        newItem.id = newId;
        itemsArray.push({ [newItem.id]: newItem });
    }

    localStorage.setItem(storageKey, JSON.stringify(itemsArray));
}

function getItemsOrderList() {
    const storageKey = 'itemsOrderList';
    let storedData = localStorage.getItem(storageKey);
    return storedData ? JSON.parse(storedData) : [];
}

function removeItemById(id) {

    const storageKey = 'itemsOrderList';
    const removeKey = 'itemsToRemove';

    let idsToRemove = localStorage.getItem(removeKey);
    idsToRemove = idsToRemove ? JSON.parse(idsToRemove) : [];

    // Adiciona o ID à lista, se ainda não estiver presente
    if (!idsToRemove.includes(id)) {
        idsToRemove.push(id);
        localStorage.setItem(removeKey, JSON.stringify(idsToRemove));
    }

    let storedData = localStorage.getItem(storageKey);
    let itemsArray = storedData ? JSON.parse(storedData) : [];

    // Filtra o array removendo o item com o ID correspondente
    itemsArray = itemsArray.filter(item => !item[id]);

    localStorage.setItem(storageKey, JSON.stringify(itemsArray));
    orderController();
}

function uploadIMage(response = false) {

    let el = ``;
    console.log(response)
    if ($(".send-image").length) {


        if (response && response.responseJSON && response.responseJSON.image) {
            el = `
                <div class="card my-2">
                    <div class="card-body" id="card-body-image">
                        <img src="${response.responseJSON.image ? response.responseJSON.image : "/assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid border border-1">
                    </div>
                </div>
                <hr>
            `;

            $(".send-image").html(`
                <div class="row">
                    <div class="col-lg-4 col-md-6 col-12">
                        ${el}
                    </div>
                </div>
            `);
            $("input[name='image']").val(response.responseJSON.image).trigger('change')
        } else {
            el = `
                <div class="card my-2">
                    <div class="card-body" id="card-body-image">
                        <form id="form-image" data-method="POST" data-action="/uploads/addImage/" data-ajax="default" data-callback="uploadIMage" enctype="multipart/form-data">
                            <div class="custom-file-upload" id="fileUpload">
                                <input type="file" id="image" name="image" accept=".png, .jpg, .jpeg" required>
                                <label for="image">
                                    <span>
                                        <strong>
                                            <ion-icon name="arrow-up-circle-outline" role="img" class="md hydrated" aria-label="arrow up circle outline"></ion-icon>
                                            <i id="section-animation-image">${locale.upload_image}</i>
                                        </strong>
                                    </span>
                                </label>
                            </div>
                            <div class="mt-2" id="progressContainerImage">
                                <div class="progress rounded-0 mb-0" role="progressbar" id="progressDivImage" aria-label="progressDivImage" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                    <div class="progress-bar" id="progressBarImage" style="width: 0%"></div>
                                </div>
                            </div>
                        </form>            
                    </div>
                </div>
                <hr>
            `;

            $(".send-image").html(`
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    ${el}
                </div>
            </div>
            `);
            $("#progressContainerImage").hide();

            $('#form-image').on("submit", function (e) {
                e.preventDefault();
                let progressBarImage = $("#progressBarImage");
                let progressDivImage = $("#progressDivImage");
                let progressContainerImage = $("#progressContainerImage");

                let form = $(this);
                const method = $(form).data("method");
                const url = $(form).data("action");
                const callbackName = $(form).data("callback");

                let params = {
                    method: method,
                    url: url,
                    dataType: 'json',
                    processData: false,
                    contentType: false,
                    data: new FormData($(form)[0]),
                    beforeSend: function() {
                        $("#progressContainerImage").show(500);
                        $("#section-animation-image").addClass('w-100 d-flex justify-content-center').html(spinner);
                        window.bkp_html = $('.btn-submit').html();
                        $('.btn-submit').html(spinner_upload).addClass("disabled");
                        progressBarImage.removeClass('bg-danger');
                        progressDivImage.prop('aria-valuenow', '0');
                        progressBarImage.css("width", "0%");
                    },
                    xhr: function() {
                        var xhr = new XMLHttpRequest();
                        xhr.upload.addEventListener("progress", function(event) {
                            if (event.lengthComputable) {
                                var percentComplete = Math.round((event.loaded / event.total) * 100);
                                progressBarImage.css("width", percentComplete + "%");
                                progressDivImage.prop('aria-valuenow', percentComplete);
                            }
                        }, false);
                        return xhr;
                    },
                    complete: function(response) {
                        $('.btn-submit').html(window.bkp_html).removeClass("disabled");
                        if (callbackName && typeof window[callbackName] === 'function') {
                            window[callbackName](response);
                        }
                        setTimeout(function() {
                            progressContainerImage.hide(500);
                            $("#section-animation-image").html(locale.upload_image);
                        }, 2000);
                    }
                };

                $.ajax(params).then(function (response) {});
            });

            $('#image').on("change", function() {
                $('#form-image').submit();
            });
        }
    }
}

function _alert(icon, message, type){

    $('.custom-alert').html(`

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" id="custom-alert" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `)

}

function custom_alert(icon, message, type){

    $('.sub-custom-alert').html(`

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" id="sub-custom-alert" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `)

}

function input_phone_number(locale){

    $(`input[type="tel"]`).prop("disabled", true).parent().parent().append(`
        <input id="ddi-phone-number" name="ddi-phone-number" type="hidden">
        <input id="country-phone-number" name="country-phone-number" type="hidden">
    `)

    switch(locale){
        case 'es':
            locale = "es"
            $("#ddi-phone-number").val("+34")
            break
        case 'pt':
            locale = "br"
            $("#ddi-phone-number").val("+55")
            break
        case 'en':
            locale = "us"
            $("#ddi-phone-number").val("+1")
            break
        default:
            locale = "br"
            $("#ddi-phone-number").val("+55")
            break
    }
    const input = document.querySelector('input[type="tel"]');
    const iti = window.intlTelInput(input, {

        initialCountry: locale,
        showSelectedDialCode: true,
        utilsScript: "/assets/js/plugins/intlTelInput/build/js/utils.min.js",
    });

    iti.promise.then(() => {
        $(`input[type="tel"]`).prop('disabled', false)
    });
}

function actionForm(action, data=false){

    let global_action_sheet_content = $('#global-action-sheet-content');
    let global_action_sheet_title = $('#global-action-sheet-title');
    let body = ``;
    let route = "";
    let method = "POST";

    $("#child-global-custom-alert").remove()

    // Only Create
    switch (action){
        case 'addUser':

            route = "/new-account/";

            //For legends
            window.permissions = window.systemPermissions

            let values_select = ""
            for (let permission of window.systemPermissions){
                values_select += `
                    <option value="${permission.id}">${permission.name}</option>
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
                    <option value="${language.language}">${language.label}</option>
                `
            }

            global_action_sheet_title.html(locale.create_account);
            body = `
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_name}</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="${locale.input_name}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="username">${locale.input_email}</label>
                        <input type="email" class="form-control" id="username" name="username" autocomplete="username" placeholder="${locale.input_email}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
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
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="suggestPassword" name="suggestPassword" checked>
                    <label class="form-check-label" for="suggestPassword">${locale.generate_password}</label>
                </div>

                <div class="form-group basic password-wrapper" hidden>
                    <div class="input-wrapper">
                        <label class="label" for="password">${locale.input_password}</label>
                        <input type="password" class="form-control" id="password" name="password" onkeyup="checkPassword()"
                        autocomplete="new-password" placeholder="${locale.input_password}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group basic password-wrapper" hidden>
                    <div class="input-wrapper">
                        <label class="label" for="new-password">${locale.input_confirm_password}</label>
                        <input type="password" class="form-control" id="confirm-password" name="confirm-password" onkeyup="checkPassword()"
                               autocomplete="new-password" placeholder="${locale.input_confirm_password}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="custom-alert my-2"></div>
            `;

            break;
    }

    //Only Update
    switch (action){
        case 'addSubCategory':
        case 'editSubCategory':

            route = `/settings/subcategory/${data.id}/`;
            method = "PUT";
            if (action === "addSubCategory"){
                route = `/settings/subcategory/`;
                method = "POST";
            }


            global_action_sheet_title.html(locale.menu_item_sub_category);

            let select = "";
            for (const glass_type of window.defaultValues.types) {
                select += `
                <option value="${glass_type.id}" ${(data.glass_type_id === glass_type.id) ? "selected" : "" }>
                    ${glass_type.name}
                </option>`;
            }

            body = `

                <input type="hidden" name="category_id" value="${window.category_id}">
                <input type="hidden" name="image" value="${data.image ? data.image : ""}">

                <p class="form-check-label">
                    ${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}
                </p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active-subcategory" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active-subcategory">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="additional_name">${locale.input_additional_description}</label>
                        <input type="text" class="form-control" id="additional_name" name="additional_name" value="${data.additional_name ? data.additional_name : ""}" placeholder="${locale.input_additional_description}">
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-type">${locale.menu_item_glass_type}</label>
                        <select class="form-control custom-select" id="select-type" name="type">
                            <option value="" selected disabled>Selecione uma opção</option>
                            ${select}
                        </select>
                        <label class="text-warning label mt-1" id="legend-permission"></label>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case 'addCategory':
        case 'editCategory':

            route = `/settings/category/${data.id}/`;
            method = "PUT";
            if (action === "addCategory"){
                route = `/settings/category/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_category);

            body = `
                <p class="form-check-label">
                    ${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}
                </p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;
            break;

        case 'addFinish':
        case "editFinish":

            route = `/settings/glass_finish/${data.id}/`;
            method = "PUT";
            if (action === "addFinish"){
                route = `/settings/glass_finish/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_glass_finish);

            body = `
                <p class="form-check-label" >${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}</p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case 'addEmails':
        case "editEmails":

            route = `/settings/emails/${data.id}/`;
            method = "PUT";
            if (action === "addEmails" || !data.id){
                route = `/settings/emails/`;
                method = "POST";
            }

            global_action_sheet_title.html(`${locale.menu_item_emails} - ${data.title}`);

            body = `
                <input type="hidden" name="key" value="${data.registry_key}" >
                <p class="form-check-label" >${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}</p>
                <hr>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="email">${locale.input_email}</label>
                        <input type="text" class="form-control" id="email" name="email" value="${data.value ? data.value : ""}" placeholder="${locale.input_email}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case 'addType':
        case "editType":

            route = `/settings/glass_type/${data.id}/`;
            method = "PUT";
            if (action === "addType"){
                route = `/settings/glass_type/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_glass_type);

            body = `
                <p class="form-check-label" >${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}</p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case "addThickness":
        case "editThickness":

            route = `/settings/glass_thickness/${data.id}/`;
            method = "PUT";
            if (action === "addThickness"){
                route = `/settings/glass_thickness/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_glass_thickness);

            // Criar o select com as opções dinâmicas
            let selectHtml = '';
            for (const category in window.units) {
                if (window.units.hasOwnProperty(category)) {
                    selectHtml += `<optgroup label="${window.units[category].title[window.locale]}">`;

                    window.units[category].units.forEach(unit => {
                        selectHtml += `<option value="${category}/${unit.symbol}" ${(data.category === category && data.type === unit.symbol) ? "selected" : "" }>${unit.name[window.locale]} (${unit.symbol})</option>`;
                    });

                    selectHtml += '</optgroup>';
                }
            }

            body = `
                <p class="form-check-label">
                    ${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}
                </p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-type">${locale.input_unit}</label>
                        <select class="form-control custom-select" id="select-type" name="type" required>
                            <option value="" selected disabled>Selecione uma opção</option>
                            ${selectHtml}
                        </select>
                        <label class="text-warning label mt-1" id="legend-permission"></label>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="price">${locale.input_price}</label>
                        <input type="tel" class="form-control" id="price" name="price" value="${data.price ? data.price*100 : ""}" placeholder="${locale.input_price}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case "addColor":
        case "editColor":

            route = `/settings/glass_colors/${data.id}/`;
            method = "PUT";
            if (action === "addColor"){
                route = `/settings/glass_colors/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_glass_colors);

            body = `
                <p class="form-check-label">
                    ${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}
                </p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="percent">${locale.input_percent}</label>
                        <input type="tel" class="form-control" id="percent" name="percent" value="${data.percent ? data.percent*100 : 0}" placeholder="${locale.input_percent}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case "addClearance":
        case "editClearance":

            route = `/settings/glass_clearances/${data.id}/`;
            method = "PUT";
            if (action === "addClearance"){
                route = `/settings/glass_clearances/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_glass_clearances);
            body = `
                <p class="form-check-label">
                    ${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}
                </p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="width">${locale.input_width} (cm)</label>
                        <input type="tel" class="form-control" id="width" name="width" value="${data.width ? data.width*100 : 0}" placeholder="${locale.input_width}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="width">${locale.input_height} (cm)</label>
                        <input type="tel" class="form-control" id="height" name="height" value="${data.height ? data.height*100 : 0}" placeholder="${locale.input_height}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case "addPrintTemplates":
        case "editPrintTemplates":

            route = `/settings/print_templates/${data.id}/`;
            method = "PUT";
            if (action === "addPrintTemplates"){
                route = `/settings/print_templates/`;
                method = "POST";
            }

            global_action_sheet_title.html(locale.menu_item_print_templates);

            let iframe = ``
            if (data.width && data.height && data.spacing){
                iframe = `
                <div class="my-2">
                    <iframe id="printIframe" style="width: 100%" height="auto" src="__url__"></iframe>
                </div>
                `;
            }

            body = `
                <p class="form-check-label" >${data.created_text ? `${locale.label_created} ${data.created_text}`: locale.label_new_registry}</p>
                <hr>
                
                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" ${data.active ? "checked" : ""}>
                    <label class="form-check-label" for="active">${locale.input_active}</label>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name">${locale.input_description}</label>
                        <input type="text" class="form-control" id="name" name="name" value="${data.name ? data.name : ""}" placeholder="${locale.input_description}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="width">${locale.input_width} (mm)</label>
                        <input type="tel" class="form-control" id="width" name="width" value="${data.width ? data.width*100 : ""}" placeholder="${locale.input_width}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="height">${locale.input_height} (mm)</label>
                        <input type="tel" class="form-control" id="height" name="height" value="${data.height ? data.height*100 : ""}" placeholder="${locale.input_height}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="spacing">${locale.input_spacing} (mm)</label>
                        <input type="tel" class="form-control" id="spacing" name="spacing" value="${data.spacing ? data.spacing : ""}" placeholder="${locale.input_spacing}" required>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                
                ${iframe ? iframe.replace('__url__', "/print/teste/1/1") : ""}
                
                <!--
                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="model">${locale.input_model} (html)</label>
                        <textarea class="form-control" rows="6" id="model" name="model" placeholder="${locale.input_model}" required>${data.model ? data.model : ""}</textarea>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>
                -->
                
                <div id="child-global-custom-alert" class="custom-alert my-2"></div>
            `;

            break;

        case 'showDescriptionProduct':

            global_action_sheet_title.html(locale.product_description);

            data.product_id = data.id;
            data.id = false


            body = `
            
            <div class="form-check-label">
                <b>${locale.input_description}:</b> 
                <br> ${data.name ? data.name : ""}
            </div>
            <div class="form-check-label my-2">
                <b>${locale.input_additional_description}:</b> 
                <br> ${data.custom_name ? data.custom_name : ""}
            </div>
            <div class="form-check-label my-2">
                <b>${locale.menu_item_glass_type}:</b> 
                <br> ${data.glass_type_name ? data.glass_type_name : ""}
            </div>
            <div class="form-check-label my-2">
                <b>${locale.menu_item_category}:</b> 
                <br> ${data.category_name ? data.category_name : ""}
            </div>
            <div class="form-check-label my-2">
                <b>${locale.menu_item_sub_category}:</b> 
                <br> ${data.sub_category_name ? data.sub_category_name : ""}
            </div>
            <div class="form-check-label my-2">
                ${data.str_created ? `${locale.label_created} ${data.str_created}`: locale.label_new_registry}
            </div>
            <hr>
            <div class="row">
                <div class="col-lg-4 col-md-6 col-12">
                    <img src="${data.image ? data.image : "/assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid border border-1">
                </div>            
            </div>
            
            `;
            break;
    }

    let formBody = ``;
    if(!data.is_order){

        formBody = `

            <div class="send-image"></div>
        
            <form data-method="${method}" data-action="${route}" data-ajax="default" data-callback="">
                <input type="hidden" name="generate-link" value="1">
                ${body}
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
        `;
    }else{
        formBody = `
        
        ${body}
        <div class="row mt-2">
            <div class="col-6">
                <button type="button" class="btn btn-lg btn-cancel btn-outline-secondary btn-block" data-bs-dismiss="modal">
                    ${locale.label_btn_cancel}
                </button>
            </div>
            <div class="col-6">
                <button type="submit" class="btn btn-lg btn-primary btn-block btn-import">
                    ${locale.label_btn_import}
                </button>
            </div>
        </div>
        
        `;
    }

    global_action_sheet_content.html(formBody)

    $("#suggestPassword").on("change", function(){

        if($(this).is(':checked')){
            $(".password-wrapper").prop('hidden', true)
            $('input[type="password"]').prop('required', false).val("")
        }else{
            $(".password-wrapper").prop('hidden', false)
            $('input[type="password"]').prop('required', true)
        }

    })

    $(".btn-import").on('click', function (){
        importData(data);
    })

    function adjustIframeHeight() {
        var iframe = document.getElementById('printIframe');
        iframe.style.height = iframe.contentWindow.document.documentElement.scrollHeight + 'px';
    }

    if($('#printIframe').length){
        document.getElementById('printIframe').onload = adjustIframeHeight;
    }

    $(`input[type="tel"]`).mask('000,000.00', { reverse: true })

    $("form").on('submit', function (e){

        let form = $(this);
        const ajaxMod = $(form).data('ajax')
        if (ajaxMod === "default"){

            e.preventDefault();
            processForm(form);

        }
    })

    if($("input[name='image']").length){
        uploadIMage();
    }


}

function close_global_modal(){
    const actionSheetForm = new bootstrap.Modal('#actionSheetForm')
    actionSheetForm.hide()
}

function global_alert(response, time){

    let type = "";
    let icon = "";
    switch (response.status) {
        case "success":
            type = "primary";
            icon = `alert-circle-outline`;
            break;
        case "warning":
            type = "warning";
            icon = `warning-outline`
            break;
        case "error":
            type = "danger";
            icon = `close-circle`;
            break;
        default:
            type = "info";
            icon = `information-circle-outline`;
            break;
    }

    const alert_html = `

    <div class="alert alert-imaged alert-${type} alert-dismissible fade show mb-2" id="global-alert-container" role="alert">
        <div class="icon-wrap">
            <ion-icon name="${icon}" role="img" class="md hydrated" aria-label="${icon}"></ion-icon>
        </div>
        <div>
            ${response.message}
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    
    `;

    if ($('#child-global-custom-alert').length && !window.use_global_alert) {
        $('#child-global-custom-alert').html(alert_html).hide().show(500)
    }else{
        $('#global-custom-alert').html(alert_html).hide().show(500)
    }

    if(time){

        setTimeout(() => {

            close_global_modal();

            let bsAlert = new bootstrap.Alert('#global-alert-container')
            if ($('#child-global-custom-alert').length && !window.use_global_alert) {
                bsAlert = new bootstrap.Alert('#child-global-custom-alert')
            }
            window.use_global_alert = false;
            bsAlert.close()

        }, time*1000)

    }

}

function dialog(response, time) {

    let dialogTheme = "";
    let icon = "";
    switch (response.status) {
        case "success":
            dialogTheme = "primary";
            icon = `<ion-icon name="checkmark-circle-outline"></ion-icon>`;
            break;
        case "warning":
            dialogTheme = "warning";
            icon = `<ion-icon name="warning-outline"></ion-icon>`
            break;
        case "error":
            dialogTheme = "danger";
            icon = `<ion-icon name="close-circle"></ion-icon>`;
            break;
        default:
            dialogTheme = "info";
            icon = `<ion-icon name="information-circle-outline"></ion-icon>`;
            break;
    }

    close_global_modal();

    $('#dialog-container').html(`
        <div class="modal fade dialogbox" id="DialogIconed" data-bs-backdrop="static" tabIndex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-icon text-${dialogTheme}">
                        ${icon}
                    </div>
                    <div class="modal-header"></div>
                    <div class="modal-body">
                        ${response.message}
                    </div>
                    <div class="modal-footer">
                        <div class="btn-inline">
                            <a href="#" class="btn" data-bs-dismiss="modal">OK</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `)

    if(time){
        auto_remove_alert(time)
    }
    $("#DialogIconed").modal('toggle')

}

function dialogQuestion(response){

    let dialogTheme = "";
    let icon = "";
    switch (response.status) {
        case "success":
            dialogTheme = "primary";
            icon = `<ion-icon name="checkmark-circle-outline"></ion-icon>`;
            break;
        case "warning":
            dialogTheme = "warning";
            icon = `<ion-icon name="warning-outline"></ion-icon>`
            break;
        case "error":
            dialogTheme = "danger";
            icon = `<ion-icon name="close-circle"></ion-icon>`;
            break;
        default:
            dialogTheme = "info";
            icon = `<ion-icon name="information-circle-outline"></ion-icon>`;
            break;
    }

    $('#dialog-container').html(`
    
    <div class="modal fade dialogbox" id="DialogQuestion" data-bs-backdrop="static" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-icon text-${dialogTheme}">
                    ${icon}
                </div>
                <div class="modal-header pt-2">
                    <h5 class="modal-title">${response.title}</h5>
                </div>
                <div class="modal-body">
                    ${response.question}
                </div>
                <div class="modal-footer">
                    <div class="btn-inline">
                        <a href="#" class="btn btn-text-secondary" data-bs-dismiss="modal">${locale.label_btn_cancel}</a>
                        <a href="#" class="btn btn-text-primary" data-bs-dismiss="modal" onclick="saveOrder(true, false)">OK</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    `)

    $("#DialogQuestion").modal('toggle')
}

function auto_remove_alert(time) {

    setTimeout(() => {

        let bsAlertMain = new bootstrap.Alert('#custom-alert')
        let bsAlertSub = new bootstrap.Alert('#sub-custom-alert')

        try{
            bsAlertMain.close()
        }catch (e){

        }

        try{
            bsAlertSub.close()
        }catch (e){

        }


    }, time * 1000)

}

function checkPassword() {

    const password = $("input#password").val()
    const new_password = $("input#confirm-password").val()

    if (password === new_password) {

        if (password.length >= 8 && new_password.length >= 8) {
            $('.btn-submit').attr("disabled", false)
            auto_remove_alert(0)
        } else {
            _alert("alert-circle-outline", locale.password_verify_length, "danger")
        }

    } else {
        _alert("alert-circle-outline", locale.password_verify_match, "danger")
        $('.btn-submit').attr("disabled", true)
    }
}

function formatString(value, mask, params = {}) {

    if (!value) {
        return 'Não informado'
    }

    let $tempInput = $('<input>').val(value).mask(mask, params);
    return $tempInput.val();
}

function toast_alert(response, title=null){

    let toastTheme = "";
    switch (response.status) {
        case "success":
            toastTheme = "primary";
            break;
        case "warning":
            toastTheme = "warning";
            break;
        case "error":
            toastTheme = "danger";
            break;
        default:
            toastTheme = "info";
            break;
    }

    let timer = 1.5;
    let timer_global_alert = 3;
    if(response.custom_timer){
        timer = response.custom_timer
        timer_global_alert = response.custom_timer
    }

    if(response.message){

        if(response.dialog){
            dialog(response);
        }else{
            global_alert(response, timer_global_alert)
        }

    }

    if(response.spinner){
        $('.btn-submit').html(spinner).attr("disabled", true)
    }

    if (response.url) {
        setTimeout(() => {
            window.location.href = response.url;
        }, timer*1000)
    }

    if (response.reload){
        setTimeout(() => {
            window.location.reload()
        }, timer*1000)
    }

}

function change(url=false, id=false, el=false){

    const url_call = `${url}${id}/`;
    let prop = false

    if(el){

        $('[id^="SwitchCheck"]').each(function() {
            let el = this
            prop = $(el).is(':checked')
            if (el.value === id){
                window.SwitchCheckUser = id
                $(el).prop('checked', !prop).prop('disabled', true)
            }else{
                $(el).prop('disabled', true)
            }

        });

        params = {
            method: "PUT",
            url: `${url_call}`,
            dataType: 'json',
        }
        window.use_global_alert = true;
        processForm(false, params, 'change', false, false, false)

    }else{

        $('[id^="SwitchCheck"]').each(function() {
            let el = this
            prop = $(el).is(':checked')

            if (el.value === window.SwitchCheckUser){
                $(el).prop('checked', !prop).prop('disabled', false);
            }else{
                $(el).prop('disabled', false);
            }
        });
    }
}

async function sendGlobalAjax(params, progressBar=false) {
    try {
        return await $.ajax(params);
    } catch (error) {

        console.log(error)

        if (error.responseJSON){
            toast_alert(error.responseJSON)
        }else{
            toast_alert({
                "status": "error",
                "message": "Erro ao processar o formulário"
            });
            console.log("Erro ao processar o formulário:", error.responseText);
        }


        if(progressBar){
            progressBar.addClass('bg-danger')
        }

        return false;
    }
}

/* Eemple Usage: data-method="PUT" data-action="{{ request.path }}" data-ajax="default" data-callback="get_datails" */
function processForm(form=false, params=false, callbackName=false, callbackParams=false, custom_spinner=false, progresBarEnabled=false) {

    let progressBar = $("#progressBar");
    let progressDiv = $("#progressDiv");
    let progressContainer = $("#progressContainer");

    if (!form && !params){
        toast_alert({
            "status": "error",
            "message": "A requisição não pode ser iniciada, pois é obrigatório um formulário e/ou paraâmetros personalizados."
        });
        return
    }

    if (form){

        const method = $(form).data("method");
        const url = $(form).data("action");
        if (!callbackName){
            callbackName = $(form).data("callback")
        }

        if(!callbackParams){
            callbackParams = $(form).data("callbackParams")
        }

        const formDataMethod = ['POST', 'PUT']

        if (!params){
            params = {
                method: method,
                url: url,
                dataType: 'json',
            }
        }

        let formDataValues = null
        if (formDataMethod.includes(method.toUpperCase())){
            formDataValues = new FormData($(form)[0]);
            params.processData = false;
            params.contentType = false;
        }else{
            formDataValues = $(form).serialize();
        }

        params.data = formDataValues;
    }

    params.beforeSend = function() {

        if (custom_spinner){
            $("#section-animation").html(spinner)
        }

        window.bkp_html = $('.btn-submit').html()
        $('.btn-submit').html(spinner).addClass("disabled")

        progressContainer.css('z-index', 9999)

        if(progresBarEnabled){
            progressContainer.removeClass('bg-danger').show(500);
        }

        progressBar.removeClass('bg-danger');
        progressDiv.prop('aria-valuenow', '0');
        progressBar.css("width", "0%");
    }

    params.xhr = function() {
        var xhr = new XMLHttpRequest();
        xhr.upload.addEventListener("progress", function(event) {
            if (event.lengthComputable) {
                var percentComplete = Math.round((event.loaded / event.total) * 100);
                progressBar.css("width", percentComplete + "%");
                progressDiv.prop('aria-valuenow', percentComplete)
            }
        }, false);
        return xhr;
    }

    params.complete = function(response) {

        $('.btn-submit').html(window.bkp_html).removeClass("disabled")
        if (callbackName && typeof window[callbackName] === 'function') {
            window[callbackName](response, callbackParams);
        }

        close_global_modal();

        setTimeout(function() {
            progressContainer.hide(500);
            if (custom_spinner){
                $("#section-animation").hide(500).html("")
            }
        }, 2000);
    }

    sendGlobalAjax(params, progressBar).then(function (response){
        toast_alert(response);
    })
}

function searchElements(searchTerm, containerClass, itemClass) {
    const containers = document.querySelectorAll(`.${containerClass}`);
    containers.forEach(container => {
        const items = container.querySelectorAll(`.${itemClass}`);
        items.forEach(item => {
            if (item.textContent.toLowerCase().includes(searchTerm.toLowerCase())) {
                $(item).show();
            } else {
                $(item).hide();
            }
        });
    });
}

function get_cep(cep){

    if (cep.length === 9){
        _alert("alert-circle-outline", "Buscando informações...", "primary")
        $.ajax({
            url: `https://viacep.com.br/ws/${cep.replace('-', '')}/json/`,
            success:function (data){
                auto_remove_alert(0)

                $("#zone").val((data.bairro ? data.bairro : "")).trigger('change')
                $("#complement").val((data.complemento ? data.complemento : "")).trigger('change')
                $("#address").val((data.logradouro ? data.logradouro : "")).trigger('change')
                $("#state").val((data.uf ? data.uf : "")).trigger('change')
                $("#city").val((data.localidade ? data.localidade : "")).trigger('change')

            },
            error: function (error){
                console.log(error)
            }
        })
    }else{
        _alert("alert-circle-outline", locale.invalid_cep, "danger")
    }

}

function get_cnpj(cnpj){

    if(cnpj.length === 18){

        $.ajax({
            url: `https://publica.cnpj.ws/cnpj/${cnpj.replace('-', '').replace('.', '').replace('.', '').replace('/', '')}`,
            method: "GET",
            success:function (data){
                auto_remove_alert(0)

                $("#zone").val((data.bairro ? data.bairro : "")).trigger('change')
                $("#complement").val((data.complemento ? data.complemento : "")).trigger('change')
                $("#address").val((data.logradouro ? data.logradouro : "")).trigger('change')
                $("#state").val((data.estado ? data.estado.nome : "")).trigger('change')
                $("#city").val((data.cidade ? data.cidade.nome : "")).trigger('change')
                $("#phone_number").val((data.ddd1 && data.telefone1 ? `${data.ddd1}${data.telefone1}`  : "")).trigger('change')
                $("#company_name").val((data.razao_social ? data.razao_social : "")).trigger('change')
                $("#trading_name").val((data.nome_fantasia ? data.nome_fantasia : "")).trigger('change')
                $("#zip_code").val((data.cep ? data.cep : "")).trigger('change')

            },
            error: function (error){
            }
        })

    }else{

        custom_alert("alert-circle-outline", locale.invalid_document, "danger")
        if(cnpj.length === 14){
            auto_remove_alert(0)
        }
    }

}

$(document).ready(() => {

    $("#progressContainer").hide()

    $(".select-2").select2({
        placeholder: locale.select_2_placeholder,
        language: "pt",
        dropdownParent: $('.modal')
    });

    $("form").on('submit', function (e){

        let form = $(this);
        const ajaxMod = $(form).data('ajax')
        if (ajaxMod === "default"){

            e.preventDefault();
            processForm(form);

        }
    })
})
