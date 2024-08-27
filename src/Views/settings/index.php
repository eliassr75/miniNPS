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
            <ul class="listview image-listview inset list my-2">
                <?php foreach ($menu_options as $menu_option): ?>
                <li id="li-model">
                    <a href="<?=$menu_option->url?>" class="item">
                        <div class="icon-box bg-primary">
                            <ion-icon name="<?=$menu_option->icon?>"></ion-icon>
                        </div>
                        <div class="in">
                            <?=$menu_option->name?>
                            <?php if($menu_option->badge): ?>
                                <span class="badge badge-primary"><?=$menu_option->badge?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
