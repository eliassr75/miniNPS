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
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-body my-2">

                        <form data-method="PUT" data-action="/settings/category/<?=$category->id?>" data-ajax="default" data-callback="">

                            <p class="form-check-label">
                                <?=$functionController->locale('label_created')?>: <?=date('d/m/Y H:i', strtotime($category->created_at))?>
                            </p>
                            <hr>

                            <div class="form-check my-2">
                                <input type="checkbox" class="form-check-input" id="active" name="active" <?=$category->active ? "checked" : ""?>>
                                <label class="form-check-label" for="active"><?=$functionController->locale('input_active')?></label>
                            </div>
                            <div class="form-group basic">
                                <div class="input-wrapper">
                                    <label class="label" for="name"><?=$functionController->locale('input_description')?></label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?=$category->name?>" placeholder="<?=$functionController->locale('input_description')?>" required>
                                    <i class="clear-input">
                                        <ion-icon name="close-circle"></ion-icon>
                                    </i>
                                </div>
                            </div>

                            <div class="section full my-2">
                                <div class="accordion border-0" id="accordionManager">
                                    <div class="accordion-item border">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#accordion-price-manager" aria-expanded="false">
                                                <ion-icon name="wallet-outline" role="img" class="md hydrated" aria-label="wallet outline"></ion-icon>
                                                <?=$functionController->locale('label_price_manager')?>
                                            </button>
                                        </h2>
                                        <div id="accordion-price-manager" class="accordion-collapse collapse" data-bs-parent="#accordionManager" style="">
                                            <div class="accordion-body">
                                                <?php foreach ($thickness as $thick): ?>
                                                    <div class="form-group basic">
                                                        <div class="input-wrapper">
                                                            <label class="label" for="input-<?=$thick->name?><?=$thick->type?>"><?=$functionController->locale('input_price')?> - <?=$thick->name?><?=$thick->type?></label>
                                                            <input type="tel" class="form-control" id="input-<?=$thick->name?><?=$thick->type?>" name="input-<?=$thick->name?><?=$thick->type?>"
                                                                   value="<?=$thick->price*100?>" placeholder="<?=$functionController->locale('input_price')?> - <?=$thick->name?><?=$thick->type?>">
                                                            <i class="clear-input">
                                                                <ion-icon name="close-circle"></ion-icon>
                                                            </i>
                                                        </div>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="child-global-custom-alert" class="custom-alert my-2"></div>

                            <div class="row mt-2">
                                <div class="col-12">
                                    <button type="submit" class="btn btn-lg btn-primary btn-block btn-submit">
                                        <?=$functionController->locale('label_btn_save')?>
                                    </button>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
                <div class="col-md-9 my-2">
                    <div class="card card-body">
                        <p class="form-check-label d-inline-flex align-items-center justify-content-between">
                            <?=$functionController->locale('menu_item_sub_category')?>
                            <a class="btn btn-primary" id="headerButton" href="javascript:void(0)" onclick="actionForm('addSubCategory')"
                               data-bs-toggle="modal" data-bs-target="#actionSheetForm">
                                <ion-icon name="add-outline"></ion-icon>
                                <?=$functionController->locale('label_btn_add')?>
                            </a>
                        </p>
                        <div class="form-group boxed border-top">
                            <div class="input-wrapper">
                                <label class="label" for="select-glass-type"><?=$functionController->locale('menu_item_glass_type')?></label>
                                <select class="form-control custom-select" id="select-glass-type" name="glass-type" onchange="changeFilter(this)">
                                    <option value="">Todos</option>
                                    <?php foreach ($default['types'] as $item):
                                        echo "<option value='" . $item['id'] . "'>" . $item['name'] . "</option>";
                                    endforeach;?>
                                </select>
                            </div>
                        </div>

                        <form class="search-form">
                            <div class="form-group searchbox">
                                <input type="text" class="form-control search" id="searchInput" placeholder="<?=$functionController->locale('input_label_search')?>">
                                <i class="input-icon">
                                    <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                                </i>
                            </div>
                        </form>

                        <hr>

                        <?php if (count($category->sub_categories()->get())): ?>
                            <div class="row list" id="rows"></div>
                        <?php else: ?>
                            <div class="alert alert-primary" role="alert">
                                <?= $functionController->locale('not_found_results') ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>
    window.defaultValues = <?=$functionController->parseObjectToJson($default)?>;
    window.category_id = <?=$category->id?>;

    function editSubCategory(data){

        const actionSheetForm = new bootstrap.Modal('#actionSheetForm')
        actionSheetForm.show()
        actionForm('editSubCategory', data)

    }

    function changeFilter(el){
        window.glassType = el.value
        categoryController()
    }

    function categoryController(response=false){

        if (!response){

            params = {
                method: "GET",
                url: `${window.location.pathname}json/`,
                data: {
                    type: window.glassType
                },
                dataType: 'json',
            }
            processForm(false, params, "categoryController", false, true)
        }

        if (response) {
            response = response.responseJSON;
            console.log(response)

            let fields = ``
            for (let values of response.values.subCategories){

                fields += `

                <div onclick='editSubCategory(${JSON.stringify(values)})' class="col-lg-2 col-md-4 col-6 searchable">
                    <div class="border border-1 p-1 my-1 rounded-2 search-item">
                        <div class="text-truncate font-weight-bold ">${values.name}</div>
                        <div class="text-truncate">
                            <span class="">${values.glass_type ? values.glass_type.name : ""}</span> -
                            <span class="">${values.additional_name ? values.additional_name : ""}</span>
                        </div>
                        <img src="${values.image ? values.image : "assets/img/sample/photo/1.jpg"}" alt="image" class="imaged img-fluid">
                    </div>
                </div>

                `;

            }

            $('#rows').html(fields)

            document.getElementById('searchInput').addEventListener('input', function() {
                const searchTerm = this.value;
                searchElements(searchTerm, 'searchable', 'search-item');
            });
        }

    }

    $(document).ready(() => {
        categoryController();
        $(`input[type="tel"]`).mask('000,000.00', { reverse: true })
    })

</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
