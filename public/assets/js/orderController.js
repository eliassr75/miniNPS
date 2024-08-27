const actionSheetForm = new bootstrap.Modal('#actionSheetForm')
const required_string = `<ion-icon name="alert-circle-outline"></ion-icon>`;

function getNameItem(category_id, sub_category_id){

    let listNamesCategories = {};
    let listNamesSubCategories = {};

    for (let c of categories){
        listNamesCategories[c.id] = c.name
    }
    for (let c of subCategorias[category_id]){
        listNamesSubCategories[c.id] = c.name
    }

    return `${listNamesCategories[category_id]} - ${listNamesSubCategories[sub_category_id]}`

}

function formatCurrency(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

function getLogs(){

    let log_item = "";
    if (existsOrder.order){
        if (logs.length){
            for (let log of logs){
                log_item += `
                <p class="text-truncate border-bottom"> 
                    <!--<button class="btn btn-primary btn-sm btn-icon"><ion-icon name="eye-outline"></ion-icon></button>-->
                    ${log.str_created} -> ${log.description}
                </p>
            `;
            }
        }else{
            log_item = `
            <div class="alert alert-primary" role="alert">
                ${locale.not_items_founded}
            </div>
        `;
        }

        $("#logEntry").html(log_item);
    }else{
        $('#accordionLogs').parent().hide(0);
    }


}

function checkExistsOrder(){

    if(existsOrder.order){

        localStorage.setItem('selected_client_id', existsOrder.order.client_id);
        localStorage.setItem('selected_status_id', existsOrder.order.status_id);
        localStorage.setItem('selected_finance_id', existsOrder.order.finance_id);
        localStorage.setItem('total_price_order', existsOrder.order.total_price);
        localStorage.setItem('general_obs_client', existsOrder.order.obs_client);
        localStorage.setItem('date_delivery', existsOrder.order.date_delivery);
        localStorage.setItem('order_id', existsOrder.order.id)
        localStorage.removeItem('itemsToRemove');

        let itemsArray = [];
        for (let data of existsOrder.items){
            let model = {
                id: data.id ? data.id : null,
                order_id: data.order_id ? data.order_id : null,
                client_id: existsOrder.order ? existsOrder.order.client_id : null,
                category_id: data.category_id,
                sub_category_id: data.sub_category_id,
                glass_thickness_id: data.glass_thickness_id,
                glass_type_id: data.glass_type_id,
                glass_color_id: data.glass_color_id,
                glass_finish_id: data.glass_finish_id,
                glass_clearances_id: data.glass_clearances_id,
                product_id: data.product_id ? data.product_id : null,
                quantity: data.quantity,
                width: data.width,
                height: data.height,
                price: parseFloat(data.price),
                date_delivery: data.date_delivery,
                obs_client: data.obs_client,
                obs_tempera: data.obs_tempera,
                obs_factory: data.obs_factory,
                name: getNameItem(data.category_id, data.sub_category_id)
            }

            itemsArray.push({ [model.id]: model });
        }
        localStorage.setItem('itemsOrderListIdCounter', itemsArray.length);
        localStorage.setItem("itemsOrderList", JSON.stringify(itemsArray));
    }else{
        clearOrders();
    }
    orderController();
    getLogs();
}

function importData(data){

    let temp_data = getLocalStorageData();
    if(temp_data.id){
        data.id = temp_data.id
    }

    updateLocalStorage(data);
    actionSheetForm.toggle('hide');
    $('#products > #rows-images').hide();
    prepareOrderForm();
}

function showDescriptionProduct(data){
    actionSheetForm.show();
    data.is_order = true;
    actionForm('showDescriptionProduct', data);

}

function updateSelect() {
    $('select').select2({
        placeholder: locale.select_2_placeholder
    });
}

function addOrderItem(){

    let data = getLocalStorageData()

    let response = {
        status: "warning",
        message: `
            ${locale.warning_exists_required_fields}: <br>
            ${!data.quantity ? `<span class="text-warning">${locale.input_quantity}</span> <br>` : ""}
            ${!data.width ? `<span class="text-warning">${locale.input_width}</span> <br>` : ""}
            ${!data.height ? `<span class="text-warning">${locale.input_height}</span> <br>` : ""}
            ${!data.category_id ? `<span class="text-warning">${locale.menu_item_category}</span> <br>` : ""}
            ${!data.sub_category_id ? `<span class="text-warning">${locale.menu_item_sub_category}</span> <br>` : ""}
            ${!data.glass_thickness_id ? `<span class="text-warning">${locale.menu_item_glass_thickness}</span> <br>` : ""}
            ${!data.glass_color_id ? `<span class="text-warning">${locale.menu_item_glass_colors}</span> <br>` : ""}
            ${!data.glass_finish_id ? `<span class="text-warning">${locale.menu_item_glass_finish}</span> <br>` : ""}
            ${!data.glass_clearances_id ? `<span class="text-warning">${locale.menu_item_glass_clearances}</span> <br>` : ""}
            ${!data.glass_type_id ? `<span class="text-warning">${locale.menu_item_glass_type}</span> <br>` : ""}
        `,
    };

    if (!data.quantity || !data.width || !data.height || !data.category_id || !data.sub_category_id || !data.glass_thickness_id || !data.glass_color_id || !data.glass_finish_id || !data.glass_type_id) {
        dialog(response);
        return;
    }

    let item_model = {
        id: data.id ? data.id : null,
        order_id: data.order_id ? data.order_id : null,
        category_id: data.category_id,
        sub_category_id: data.sub_category_id,
        glass_thickness_id: data.glass_thickness_id,
        glass_color_id: data.glass_color_id,
        glass_finish_id: data.glass_finish_id,
        glass_clearances_id: data.glass_clearances_id,
        glass_type_id: data.glass_type_id,
        product_id: data.product_id ? data.product_id : null,
        quantity: data.quantity,
        width: data.width,
        height: data.height,
        price: data.price,
        obs_client: data.obs_client,
        obs_tempera: data.obs_tempera,
        obs_factory: data.obs_factory,
        name: getNameItem(data.category_id, data.sub_category_id)
    }

    updateItemsOrderList(item_model);
    orderController();
}

function editItem(id){
    let data = getItemsOrderList();
    if (data.length) {
        for (let itemObj of data) {
            let itemId = Object.keys(itemObj)[0];
            if (parseInt(itemId) === parseInt(id)){
                let item = itemObj[itemId];
                updateLocalStorage(item);
                orderController(item);
            }
        }
    }
}

function clearOrders(){
    localStorage.removeItem('itemsOrderList');
    localStorage.removeItem('jsonStorage');
    localStorage.removeItem('selected_client_id');
    localStorage.removeItem('selected_status_id');
    localStorage.removeItem('selected_finance_id');
    localStorage.removeItem('general_obs_client');
    localStorage.removeItem('date_delivery');
    localStorage.removeItem('total_price_order');
    localStorage.removeItem('itemsOrderListIdCounter');
    localStorage.removeItem('order_id');
    localStorage.removeItem('itemsToRemove');
    orderController();
}

function saveOrder(save=false, show=true){

    const client_id = localStorage.getItem('selected_client_id') ? parseInt(localStorage.getItem('selected_client_id')) : null
    const order_id = localStorage.getItem('order_id') ? parseInt(localStorage.getItem('order_id')) : null
    const status_id = localStorage.getItem('selected_status_id') ? parseInt(localStorage.getItem('selected_status_id')) : null
    const finance_id = localStorage.getItem('selected_finance_id') ? parseInt(localStorage.getItem('selected_finance_id')) : null
    const total_price_order = localStorage.getItem('total_price_order') ? parseFloat(localStorage.getItem('total_price_order')) : 0.00
    const general_obs_client = localStorage.getItem('general_obs_client') ? localStorage.getItem('general_obs_client') : ""
    const date_delivery = localStorage.getItem('date_delivery') ? localStorage.getItem('date_delivery') : ""
    const idsToRemove = localStorage.getItem('itemsToRemove') ? JSON.parse(localStorage.getItem('itemsToRemove')) : [];

    const data = getItemsOrderList();
    let response = false;

    if (!data.length){
        response = {
            status: "warning",
            message: locale.nullable_order
        }
    }else if(!date_delivery) {
        response = {
            status: "warning",
            message: `
            ${locale.warning_exists_required_fields}: <br>
            ${!date_delivery ? `<span class="text-warning">${locale.input_date_delivery}</span> <br>` : ""}
        `,
        };
    }

    if (response){
        dialog(response)
    }else{

        if(show){
            dialogQuestion({
                "status": "waning",
                "title": locale.question_title_save_order,
                "question": locale.question_message_save_order
            })
        }

        if (save){

            let params = {
                method: order_id ? "PUT" : "POST",
                url: window.location.pathname,
                data: {
                    items: data,
                    client_id: client_id,
                    total_price: total_price_order,
                    order_id: order_id,
                    status_id: status_id,
                    finance_id: finance_id,
                    obs_client: general_obs_client,
                    date_delivery: date_delivery,
                    ids_to_remove: idsToRemove.join(',')
                },
                dataType: 'json',
            }

            console.log(params)

            processForm(false, params);
        }

    }


}

function getOrderItems() {

    let data = getItemsOrderList();
    let itemsTemplate = ``;
    let total_price_order = 0.00
    const general_obs_client = localStorage.getItem('general_obs_client') ? localStorage.getItem('general_obs_client') : false
    const date_delivery = localStorage.getItem('date_delivery') ? localStorage.getItem('date_delivery') : false
    const order_id = localStorage.getItem('order_id') ? parseInt(localStorage.getItem('order_id')) : false

    itemsTemplate += `

        <div class="d-flex w-100 align-items-center justify-content-between">
            <div>
                <p>
                    ${locale.form_order_item}
                </p>
            </div>
            <div>
                ${readOnly ? "" :
                    `<button class="px-1 btn btn-primary" type="button" ${readOnly ? "" : `onclick="orderController(true);"`}>
                        <ion-icon name="add-outline"></ion-icon>
                        ${locale.label_btn_add}
                    </button>`
                }
                ${ order_id ? `
                    <button class="px-1 btn btn-primary" type="button" ${!existsOrder.order ? "" : `onclick="print('products');"`}>
                        <ion-icon name="print-outline"></ion-icon>
                        ${locale.label_tags}
                    </button>
                ` : ""}
            </div>
        </div>

        <ul class="listview image-listview my-2">
    `;

    if (data.length) {
        for (let itemObj of data) {
            let itemId = Object.keys(itemObj)[0];
            let item = itemObj[itemId];

            itemsTemplate += `
                <li class="px-0 mx-0 w-100" id="li-model-${itemId}">
                    <div class="item m-0 p-0">
                        <a href="javascript:void(0)" ${readOnly ? "disabled" : `onclick='removeItemById(${item.id})'` } class="icon-box bg-danger">
                            <ion-icon name="trash-outline" role="img" class="md hydrated" aria-label="trash outline"></ion-icon>
                        </a>
                        <a href="javascript:void(0)" onclick='editItem(${item.id})' class="in cursor">
                            <div class="text-truncate" style="max-width: 55vw">${item.quantity}x ${item.name}</div>
                            <span class="text-muted">${showPrice ? formatCurrency(item.price) : "--"}</span>
                        </a>
                    </div>
                </li>
            `;

            total_price_order += item.price;
        }
        localStorage.setItem('total_price_order', total_price_order)
    } else {
        itemsTemplate += `
            <div class="alert alert-primary" role="alert">
                ${locale.not_items_founded}
            </div>
        `;
    }

    itemsTemplate += `
        </ul>
        <br>
        <div class="form-group basic">
            <div class="input-wrapper">
                <label class="label" for="date-delivery">${required_string} ${locale.input_date_delivery}</label>
                <input type="date" class="form-control" id="date-delivery" min="${min_date}" ${readOnly ? "disabled" : ""}
                name="date-delivery" placeholder="${locale.input_date_delivery}" value="${date_delivery ? date_delivery : ""}" required>
                <i class="clear-input">
                    <ion-icon name="close-circle"></ion-icon>
                </i>
            </div>
        </div>
        <div class="form-group basic">
            <div class="input-wrapper">
                <label class="label" for="general-obs-client"> ${locale.input_obs_client}</label>
                <textarea class="form-control" rows="6" id="general-obs-client" name="general-obs-client" ${readOnly ? "disabled" : ""}
                placeholder="${locale.input_obs_client}" required>${general_obs_client ? general_obs_client : ""}</textarea>
                <i class="clear-input">
                    <ion-icon name="close-circle"></ion-icon>
                </i>
            </div>
        </div>
        <div class="w-100">
            <div class="row d-flex align-items-center">
                <div class="col-lg-4 col-md-5 col-6 d-flex align-items-center">
                    ${locale.label_total_price}: ${showPrice ? formatCurrency(total_price_order.toFixed(2)) : "--"}
                </div>
                <div class="col-lg-4 col-md-5 col-6">
                    <button type="button" class="btn btn-lg btn-primary btn-block btn-submit" onclick="saveOrder()">
                        ${locale.label_btn_save}
                    </button>
                </div>
            </div>
        </div>
        
        <div class="section border-top full mt-2">
            <div class="accordion" id="accordionLogs">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordionModifications" aria-expanded="false">
                            ${locale.label_modify_history}
                        </button>
                    </h2>
                    <div id="accordionModifications" class="accordion-collapse collapse" data-bs-parent="#accordionLogs">
                        <div class="accordion-body" id="logEntry">
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;

    $('#orderItems').html(itemsTemplate).show();

    $("#date-delivery").on('change', function () {
        localStorage.setItem('date_delivery', this.value);
    })

    $("#general-obs-client").on('change', function () {
        localStorage.setItem('general_obs_client', this.value);
    })

}

function print(type){

    const order_id = localStorage.getItem('order_id') ? parseInt(localStorage.getItem('order_id')) : false
    switch (type){
        case 'order':
            window.open(`/print/order/${order_id}/`)
            break;
        case 'products':
            window.open(`/print/products/${order_id}/1/`)
            break;
    }

}

function prepareOrderForm() {

    let price = 0.00
    let nextForm = $("#nextForm");
    let nextFormBody = "";
    nextForm.html("");

    $("#select-client").parent().parent().hide();
    $("#title-page").text(locale.label_item)

    let data = getLocalStorageData();
    let subcategory_select_values = "";
    let types_select_values = "";
    let images = [];
    if(data.category_id) {

        for (let item of subCategorias[data.category_id]) {
            images[item.id] = item.image;

            if (data.sub_category_id && parseInt(data.sub_category_id) === item.id && !data.glass_type_id){
                updateLocalStorage('glass_type_id', item.glass_type_id)
            }
            subcategory_select_values += `
                <option value="${item.id}" ${data.sub_category_id && parseInt(data.sub_category_id) === item.id ? "selected" : ""}>
                    ${item.name}
                </option>
            `;
        }

        data = getLocalStorageData();

        for (let item of types) {
            types_select_values += `
                <option value="${item.id}" ${data.glass_type_id && parseInt(data.glass_type_id) === item.id ? "selected" : ""}>
                    ${item.name}
                </option>
            `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-sub-category">${required_string} ${locale.menu_item_sub_category}</label>
                    <select class="form-control custom-select" id="select-sub-category" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'sub_category_id', 'glass_type_id')" name="sub-category">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${subcategory_select_values}
                    </select>
                </div>
            </div>
            
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-type">${required_string} ${locale.menu_item_glass_type}</label>
                    <select class="form-control custom-select" id="select-glass-type" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'glass_type_id', '--')" name="glass-type">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${types_select_values}
                    </select>
                </div>
            </div>

            <div class="row">
                <div class=" col-lg-3 col-md-5 col-12 border border-1 p-1 my-1 rounded-2" id="image-item">
                </div>
            </div>

        `;
    }else{
        categories_select_values = "";
    }

    let thickness_select_values = "";
    let thickness_prices = [];
    if(data.sub_category_id) {

        if (data.category_id){
            let storedData = localStorage.getItem('customPrices');
            let itemsArray = storedData ? JSON.parse(storedData) : [];
            for (let item of itemsArray[data.category_id]) {
                thickness_prices[item.id] = parseFloat(item.price);
                thickness_select_values += `
                <option value="${item.id}" ${data.glass_thickness_id && parseInt(data.glass_thickness_id) === item.id ? "selected" : ""}>
                    ${item.name} ${item.type} - (${showPrice ? formatCurrency(item.price) : "--"})
                </option>
            `;
            }
        }else{
            for (let item of thickness) {
                thickness_prices[item.id] = parseFloat(item.price);
                thickness_select_values += `
                <option value="${item.id}" ${data.glass_thickness_id && parseInt(data.glass_thickness_id) === item.id ? "selected" : ""}>
                    ${item.name} ${item.type} - (${showPrice ? formatCurrency(item.price) : "--"})
                </option>
            `;
            }
        }


        nextFormBody += `
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="quantity">${locale.input_quantity}</label>
                    <input type="tel" class="form-control" id="quantity" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'quantity', '--')"
                    name="quantity" placeholder="${locale.input_quantity}" value="${data.quantity ? data.quantity : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="width">${locale.input_width} (cm)</label>
                    <input type="tel" class="form-control" id="width" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'width', '--')"
                    name="width" placeholder="${locale.input_width}" value="${data.width ? data.width*100 : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="height">${locale.input_height} (cm)</label>
                    <input type="tel" class="form-control" id="height" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'height', '--')"
                    name="height" placeholder="${locale.input_height}" value="${data.height ? data.height*100 : ""}" required>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-thickness">${locale.menu_item_glass_thickness}</label>
                    <select class="form-control custom-select" id="select-glass-thickness" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'glass_thickness_id', '--')" name="glass-thickness">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${thickness_select_values}
                    </select>
                </div>
            </div>
        `;

        let color_select_values = "";
        let color_percents = [];
        for (let item of colors) {
            color_percents[item.id] = parseFloat(item.percent);
            color_select_values += `
                    <option value="${item.id}" ${data.glass_color_id && parseInt(data.glass_color_id) === item.id ? "selected" : ""}>
                        ${item.name} ${showPrice ? `(R$ + ${item.percent}%)` : ""}
                    </option>
                `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-color">${locale.menu_item_glass_colors}</label>
                    <select class="form-control custom-select" id="select-glass-color" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'glass_color_id', '--')" name="glass-color">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${color_select_values}
                    </select>
                </div>
            </div>
        `;

        let finish_select_values = "";
        for (let item of finish) {
            finish_select_values += `
                <option value="${item.id}" ${data.glass_finish_id && parseInt(data.glass_finish_id) === item.id ? "selected" : ""}>${item.name}</option>
            `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-finish">${locale.menu_item_glass_finish}</label>
                    <select class="form-control custom-select" id="select-glass-finish" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'glass_finish_id', '--')" name="glass-finish">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${finish_select_values}
                    </select>
                </div>
            </div>
        `;


        let clearances_select_values = "";
        for (let item of clearances) {
            clearances_select_values += `
                <option value="${item.id}" ${data.glass_clearances_id && parseInt(data.glass_clearances_id) === item.id ? "selected" : ""}>
                    ${item.name}
                    ${typeof (item.width) == 'string' ? `(${locale.input_width}: ${item.width}${item.type})` : ""}
                    ${typeof (item.height) == 'string' ? `(${locale.input_height}: ${item.height}${item.type})` : ""}
                </option>
            `;
        }

        nextFormBody += `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-glass-clearances">${locale.menu_item_glass_clearances}</label>
                    <select class="form-control custom-select" id="select-glass-clearances" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'glass_clearances_id', '--')" name="glass-clearances">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${clearances_select_values}
                    </select>
                </div>
            </div>
        `;

        nextFormBody += `
            
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="obs-client">${locale.input_obs_client}</label>
                    <textarea class="form-control" rows="6" id="obs-client" name="obs-client" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'obs_client', '--')"
                    placeholder="${locale.input_obs_client}" required>${data.obs_client ? data.obs_client : ""}</textarea>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="obs-tempera">${locale.input_obs_tempera}</label>
                    <textarea class="form-control" rows="6" id="obs-tempera" name="obs-tempera" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'obs_tempera', '--')"
                    placeholder="${locale.input_obs_tempera}" required>${data.obs_tempera ? data.obs_tempera : ""}</textarea>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
            
            <div class="form-group basic">
                <div class="input-wrapper">
                    <label class="label" for="obs-factory">${locale.input_obs_factory}</label>
                    <textarea class="form-control" rows="6" id="obs-factory" name="obs-factory" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'obs_factory', '--')"
                    placeholder="${locale.input_obs_factory}" required>${data.obs_factory ? data.obs_factory : ""}</textarea>
                    <i class="clear-input">
                        <ion-icon name="close-circle"></ion-icon>
                    </i>
                </div>
            </div>
        
        `;

        if(data.quantity && data.glass_thickness_id && thickness_prices.length){
            price = thickness_prices[data.glass_thickness_id] * data.quantity;
            if (color_percents.length && data.glass_color_id){
                price = price + (price * color_percents[data.glass_color_id] / 100)
            }

            updateLocalStorage('price', price);
        }
    }

    nextForm.html(`
        ${nextFormBody}
        <br>
        <p>
            <b>${locale.label_total_price}: ${showPrice ? (price ? formatCurrency(price.toFixed(2))  :  0.00) : "--"}</b>
        </p>
    `);

    if(data.sub_category_id){
        $('#image-item').html(`
            <img src="${images.includes(images[data.sub_category_id]) ? images[data.sub_category_id] : "assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid">
        `)
    }

    // scrollToBottom();
    setTimeout(function (){
        updateSelect();
        $(`input[type="tel"]`).mask('000.00', { reverse: true })
    }, 0);

}

function changeValue(el, set_key, unset_key){

    let data = {};
    data[set_key] = el.value;
    data[unset_key] = false;

    const client_id = localStorage.getItem('selected_client_id') ? parseInt(localStorage.getItem('selected_client_id')) : null
    updateLocalStorage('client_id', client_id);
    updateLocalStorage(data);
    setTimeout(() => {
        data = getLocalStorageData();
        if(data.sub_category_id){
            prepareOrderForm();
        }else{
            orderController(data);
        }

    }, 0);
}

function orderController(data=false){

    let orderBody = $('#order');
    let formBory = ``;

    let status_select_values = "";
    if(orderStatus.length && !data){
        //SELECT ORDER STATUS
        let status_id = localStorage.getItem('selected_status_id') ? parseInt(localStorage.getItem('selected_status_id')) : false
        for (let item of orderStatus){
            status_select_values += `
                <option value="${item.id}" ${status_id ? `${status_id === item.id ? "selected" : ""}` : ""}>${item.name}</option>
            `;
        }
        status_select_values = `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-status">${required_string} ${locale.menu_item_status}</label>
                    <select class="form-control custom-select" id="select-status" name="status">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${status_select_values}
                    </select>
                </div>
            </div>
        `;
    }

    let finance_select_values = "";
    if(orderFinance.length && !data){
        //SELECT ORDER STATUS
        let finance_id = localStorage.getItem('selected_finance_id') ? parseInt(localStorage.getItem('selected_finance_id')) : false
        for (let item of orderFinance){
            finance_select_values += `
                <option value="${item.id}" ${finance_id ? `${finance_id === item.id ? "selected" : ""}` : ""}>${item.name}</option>
            `;
        }
        finance_select_values = `
            <div class="form-group boxed">
                <div class="input-wrapper">
                    <label class="label" for="select-finance">${required_string} ${locale.menu_item_finance}</label>
                    <select class="form-control custom-select" id="select-finance" name="finance" ${readOnly ? "disabled" : ""}>
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${finance_select_values}
                    </select>
                </div>
            </div>
        `;
    }

    //SELECT CLIENTS
    let clients_select_values = "";
    let client_id = localStorage.getItem('selected_client_id') ? parseInt(localStorage.getItem('selected_client_id')) : false
    for (let item of clients_array){
        clients_select_values += `
                <option value="${item.id}" ${client_id ? `${client_id === item.id ? "selected" : ""}` : ""}>${item.name} - ${item.document}</option>
            `;
    }
    clients_select_values = `
        <div class="form-group boxed">
            <div class="input-wrapper">
                <label class="label" for="select-client">${required_string} ${locale.menu_item_client}</label>
                <select class="form-control custom-select" id="select-client" name="client" ${readOnly ? "disabled" : ""}>
                    <option value="" selected disabled>${locale.select_2_placeholder}</option>
                    ${clients_select_values}
                </select>
            </div>
        </div>
    `;

    //SELECT CATEGORIES
    let categories_select_values = "";
    let customPrices = {};
    for (let item of categories){
        customPrices[item.id] = item.thickness;
        categories_select_values += `
            <option value="${item.id}" ${data.category_id && parseInt(data.category_id) === item.id ? "selected" : ""}>${item.name}</option>
        `;
    }

    localStorage.setItem('customPrices', JSON.stringify(customPrices))

    if(data){

        if(!client_id){
            dialog({
                "status": "warning",
                "message": `
                   ${locale.warning_exists_required_fields}: <br>
                    <span class="text-warning">${locale.menu_item_client}</span>
                `
            })
            return;
        }

        if(!data.client_id){
            localStorage.removeItem('jsonStorage');
        }

        categories_select_values = `
            <div class="form-group boxed" id="categories">
                <div class="input-wrapper">
                    <label class="label" for="select-category">${required_string} ${locale.menu_item_category}</label>
                    <select class="form-control custom-select" id="select-category" ${readOnly ? "disabled" : ""} onchange="changeValue(this, 'category_id', 'sub_category_id')" name="category">
                        <option value="" selected disabled>${locale.select_2_placeholder}</option>
                        ${categories_select_values}
                    </select>
                </div>
            </div>
        `;

        //SEARCH PRODUCTS
        let products_select_values = `
                <div id="products">
                    <form class="search-form">
                        <div class="form-group searchbox">
                            <input type="text" class="form-control search" placeholder="${locale.input_label_search}" id="searchInput" ${readOnly ? "disabled" : ""}>
                            <i class="input-icon">
                                <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                            </i>
                        </div>
                    </form>
                    <div id="rows-images">
                        <div class="row overflow-scroll overflow-visible d-flex" style="max-height: 45vh">
            `;

        if(data.category_id) {
            for (let item of products[data.category_id]) {
                products_select_values += `

                        <div onclick='showDescriptionProduct(${JSON.stringify(item)})' class="col-lg-2 col-md-4 col-6 custom-item searchable">
                            <div class="border border-1 p-1 my-1 rounded-2 search-item">
                                <div class="text-truncate font-weight-bold ">${item.name}</div>
                                <div class="text-truncate">
                                    <span class="">${item.glass_type_name ? item.glass_type_name : ""}</span>
                                </div>
                                <img src="${item.image ? item.image : "assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid">
                            </div>
                        </div>

                    `;
            }
        }
        products_select_values += `
                    </div>
                </div>
            </div>
            `;

        formBory = `
            <div>

                ${products_select_values}

                <div id="nextForm"></div>

                <div id="child-global-custom-alert" class="custom-alert my-2"></div>

                <div class="row mt-2">
                    <div class="col-6">
                        <button type="button" class="btn btn-lg btn-cancel btn-outline-secondary btn-block" onclick="orderController()">
                            ${locale.label_btn_cancel}
                        </button>
                    </div>
                    <div class="col-6">
                        <button type="button" class="btn btn-lg btn-primary btn-block btn-submit" onclick="addOrderItem()" ${readOnly ? "disabled" : ""}>
                            ${locale.label_btn_save}
                        </button>
                    </div>
                </div>
            </div>

        `;
    }else{
        categories_select_values = "";
    }

    orderBody.html(`
        
        <div class="d-flex w-100 align-items-center justify-content-between">
            <div>
                <p id="title-page">${locale.menu_item_orders}</p>
            </div>
            <div>       
                ${ existsOrder.order && !data ? `
                    <button class="px-1 btn btn-primary" type="button" ${!existsOrder.order ? "" : `onclick="print('order');"`}>
                        <ion-icon name="print-outline"></ion-icon>
                        ${locale.menu_item_orders}
                    </button>
                ` : "" }
                
                ${ client_id && !existsOrder.order ? `
                    <button class="px-1 btn btn-primary" type="button" ${readOnly ? "" : `onclick="clearOrders();"`}>
                        <ion-icon name="reload-outline"></ion-icon>
                        ${locale.label_clear}
                    </button>
                ` : ""}
            </div>
        </div>
        
    
        ${status_select_values}
        ${finance_select_values}
        ${clients_select_values}
        ${categories_select_values}
        ${formBory}
    
        <br>
        <div id="orderItems"></div>
    `);

    updateSelect();

    $("#select-client").on('change', function () {
        updateLocalStorage('client_id', this.value);
        localStorage.setItem('selected_client_id', this.value);
    })

    $("#select-status").on('change', function () {
        updateLocalStorage('status_id', this.value);
        localStorage.setItem('selected_status_id', this.value);
    })

    $("#select-finance").on('change', function () {
        updateLocalStorage('finance_id', this.value);
        localStorage.setItem('selected_finance_id', this.value);
    })

    $("#products").hide();

    if(data) {

        $('#orderItems, #products > #rows-images').hide();

        if (data.category_id){
            $('#products').show();
            if(!data.sub_category_id){
                $('#products > #rows-images').show();
                $("#searchInput").focus()
            }
        }

        document.getElementById('searchInput').addEventListener('input', function () {
            const searchTerm = this.value;
            searchElements(searchTerm, 'searchable', 'search-item');
        });

        $("#searchInput").on('focus', function () {
            $('#products > #rows-images').show();
        }).on('blur', function () {
            setTimeout(function () {
                if (!$("#searchInput").is(':focus') && !$('#rows-images').is(':hover')) {
                    $('#products > #rows-images').hide();
                }
            }, 100);
        });

        $('#rows-images').on('click', function () {
            $('#products > #rows-images').show();
        });

        prepareOrderForm();

    }else{
        getOrderItems();
    }

}

$(document).ready(() => {
    checkExistsOrder();
})