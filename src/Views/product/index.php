<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section my-2">
    <div class="card">
        <div class="card-body" id="client">

            <form data-method="<?=isset($product->id) ? "PUT" : "POST"?>"
                  data-action="/product/<?=isset($product->id) ? $product->id : "new"?>"
                  data-ajax="default" data-callback="">
                <p class="form-check-label">
                    <?php if($product->created_at):?>
                        <?=$functionController->locale('label_created')?>:
                        <?=date('d/m/Y H:i', strtotime($product->created_at))?>
                    <?php else: ?>
                        <?=$functionController->locale('label_new_registry')?>
                    <?php endif; ?>
                </p>
                <hr>

                <div class="form-check my-2">
                    <input type="checkbox" class="form-check-input" id="active" name="active" <?=$product->active ? "checked" : ""?>>
                    <label class="form-check-label" for="active"><?=$functionController->locale('input_active')?></label>
                </div>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <textarea type="text" class="form-control" id="obs" name="obs" rows="6" placeholder="<?=$functionController->locale('input_observation')?>"><?=$product->obs?></textarea>
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

                <hr>

                <p class="text-warning d-flex align-items-center">
                    <ion-icon name="information-circle-outline"></ion-icon>
                    <?=$functionController->locale('warning_automaticale_generated')?>
                </p>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="name"><?=$functionController->locale('input_description')?></label>
                        <input type="text" class="form-control" id="name" name="name" value="<?=$product->name?>" placeholder="<?=$functionController->locale('input_description')?>" readonly>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group basic">
                    <div class="input-wrapper">
                        <label class="label" for="custom_name"><?=$functionController->locale('input_additional_description')?></label>
                        <input type="text" class="form-control" id="custom_name" name="custom_name" value="<?=$product->custom_name?>" placeholder="<?=$functionController->locale('input_additional_description')?>" readonly>
                        <i class="clear-input">
                            <ion-icon name="close-circle"></ion-icon>
                        </i>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-glass-type"><?=$functionController->locale('menu_item_glass_type')?></label>
                        <select class="form-control custom-select" id="select-glass-type" name="glass-type" disabled>
                            <option value="" selected disabled><?=$functionController->locale('select_2_placeholder')?></option>
                            <?php foreach ($types as $item):
                                echo "<option value='" . $item->id . "' " . ($item->id == $product->glass_type_id ? "selected" : "") . ">" . $item->name . "</option>";
                            endforeach;?>
                        </select>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-category"><?=$functionController->locale('menu_item_category')?></label>
                        <select class="form-control custom-select" id="select-category" name="category" disabled>
                            <option value="" selected disabled><?=$functionController->locale('select_2_placeholder')?></option>
                            <?php foreach ($categories as $item):
                                echo "<option value='" . $item->id . "' " . ($item->id == $product->category_id ? "selected" : "") . ">" . $item->name . "</option>";
                            endforeach;?>
                        </select>
                    </div>
                </div>

                <div class="form-group boxed">
                    <div class="input-wrapper">
                        <label class="label" for="select-sub_category"><?=$functionController->locale('menu_item_sub_category')?></label>
                        <select class="form-control custom-select" id="select-sub_category" name="sub_category" disabled>
                            <option value="" selected disabled><?=$functionController->locale('select_2_placeholder')?></option>
                            <?php foreach ($category->sub_categories() as $item):
                                echo "<option value='" . $item->id . "' " . ($item->id == $product->sub_category_id ? "selected" : "") . ">" . $item->name . "</option>";
                            endforeach;?>
                        </select>
                    </div>
                </div>

                <div class="card my-2">
                    <div class="card-body" id="card-body-image">
                        <div class="row">
                            <div class=" col-lg-3 col-md-5 col-12">
                                <img src="<?=$product->image ? $product->image : "/assets/img/sample/photo/1.jpg"?>" alt="image" class="imaged img-fluid border border-1">
                            </div>
                        </div>
                    </div>
                </div>
            </form>

        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
