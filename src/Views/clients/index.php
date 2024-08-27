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

            <div id="clients">

                <!-- class="search" automagically makes an input a search field. -->


                <div class="px-3 pt-3 d-flex">
                    <form class="search-form">
                        <div class="form-group searchbox">
                            <input type="text" class="form-control search" placeholder="<?=$functionController->locale('input_label_search')?>">
                            <i class="input-icon">
                                <ion-icon name="search-outline" role="img" class="md hydrated" aria-label="search outline"></ion-icon>
                            </i>
                        </div>
                    </form>
                    <a href="<?=$url?>" class=" ms-2 btn btn-primary">
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
                    <?php if (count($clients) > 0): foreach ($clients as $client): ?>
                    <li id="li-model">
                        <a href="/client/<?=$client->id?>/" class="item">
                            <img src="/assets/img/sample/avatar/do-utilizador.png" alt="image" class="image">
                            <div class="in">
                                <div>
                                    <header class="document"><?=$client->document?></header>
                                    <span class="id"><?=$client->id?></span> - <span class="name"><?=$client->name ?? ($client->company_name ?? $client->trading_name)?></span>
                                        <footer class="str_created">
                                        <?=$client->str_created?>
                                    </footer>
                                </div>
                            </div>
                        </a>
                        <div class="form-check form-switch me-2">
                            <input class="form-check-input" type="checkbox" value="<?=$client->id?>" <?=$client->active ? "checked" : ""?> id="SwitchCheckClient<?=$client->id?>" onchange="change('/client/change/', <?=$client->id?>, this)">
                            <label class="form-check-label" for="SwitchCheckClient<?=$client->id?>"></label>
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
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>
    <?php if (count($clients) > 0): ?>
        $(document).ready(() => {
            let options = {
                valueNames: [ 'document', 'name', 'id', 'str_created' ]
            };
            window.clientList = new List('clients', options);
        })
    <?php endif;?>
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
