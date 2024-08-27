<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section mt-2">

    <div class="card">

        <div id="products">

            <!-- class="search" automagically makes an input a search field. -->


            <div class="px-3 pt-3">
                <form class="search-form">
                    <div class="form-group searchbox">
                        <input type="text" class="form-control search" placeholder="<?=$functionController->locale('input_label_search')?>">
                        <i class="input-icon">
                            <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                        </i>
                    </div>
                </form>
            </div>
            <!-- class="sort" automagically makes an element a sort buttons. The date-sort value decides what to sort by. -->
            <!--
            <button class="sort btn btn-primary" data-sort="name">
                Sort
            </button>
            -->

            <h3 class="inset text-center text-warning mt-2">
                <?=$functionController->locale('warning_automaticale_generated')?>
            </h3>
            <ul class="listview image-listview inset list my-2">
                <?php if (count($products) > 0): foreach ($products as $product): ?>
                <li id="li-model">
                    <a href="/product/<?=$product->id?>/" class="item">
                        <div class="in" style="max-width: 55vw">
                            <div class="w-100 text-truncate">
                                <span class="name"><?=$product->name?></span>
                                <footer class="str_created">
                                    <?=$product->str_created?>
                                </footer>
                            </div>
                        </div>
                    </a>
                    <div class="form-check form-switch me-2">
                        <input class="form-check-input" type="checkbox" value="<?=$product->id?>" <?=$product->active ? "checked" : ""?> id="SwitchCheckClient<?=$product->id?>" onchange="change('/product/change/', <?=$product->id?>, this)">
                        <label class="form-check-label" for="SwitchCheckClient<?=$product->id?>"></label>
                    </div>
                </li>
                <?php endforeach; else: ?>

                    <div class="alert alert-primary mb-2" role="alert">
                        <?=$functionController->locale('not_found_results')?>
                    </div>

                <?php endif; ?>
            </ul>
        </div>


    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>
    <?php if (count($products) > 0): ?>
        $(document).ready(() => {
            let options = {
                valueNames: [ 'document', 'name', 'str_created' ]
            };
            window.clientList = new List('products', options);
        })
    <?php endif;?>
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
