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

        <div id="categories">

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

            <ul class="listview image-listview inset list my-2">
                <?php if(count($categories)): foreach ($categories as $category):

                    $category->created_text = date('d/m/Y H:i', strtotime($category->created_at));
                    $category->categories = $category->sub_categories();

                    ?>
                    <li id="li-model">
                        <a href="/settings/category/<?=$category->id?>/" class="item">
                            <div class="icon-box bg-<?=$category->active ? "primary" : "danger"?>">
                                <ion-icon name="copy-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <span class="name"><?=$category->name?></span>
                            </div>
                        </a>
                    </li>
                <?php endforeach; else: ?>
                    <div class="alert alert-primary" role="alert">
                        <?= $functionController->locale('not_found_results') ?>
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
    $(document).ready(() => {
        let options = {
            valueNames: [ 'permission', 'name', 'str_created' ]
        };
        window.userList = new List('categories', options);
    })
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
