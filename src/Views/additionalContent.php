<?php

use App\Controllers\FunctionController;
use App\Controllers\MenuController;

$functionController = new FunctionController();
$menuController = new MenuController();

?>

<!-- App Bottom Menu -->
<?php if(in_array($_SESSION['permission_id'], [1, 2, 3])): ?>
<div class="appBottomMenu">
    <a href="/dashboard/" class="item">
        <div class="col">
            <ion-icon name="pie-chart-outline"></ion-icon>
            <strong>Dashboard</strong>
        </div>
    </a>
    <a href="/clients/" class="item">
        <div class="col">
            <ion-icon name="people-outline"></ion-icon>
            <strong><?=$functionController->locale('menu_item_client')?></strong>
        </div>
    </a>
    <a href="/orders/" class="item">
        <div class="col">
            <ion-icon name="document-text-outline"></ion-icon>
            <strong><?=$functionController->locale('menu_item_orders')?></strong>
        </div>
    </a>
    <!--
    <a href="/products/" class="item">
        <div class="col">
            <ion-icon name="apps-outline"></ion-icon>
            <strong><?=$functionController->locale('menu_item_products')?></strong>
        </div>
    </a>
    -->
    <?php if(in_array($_SESSION['permission_id'], [1])): ?>
        <a href="/settings/" class="item">
            <div class="col">
                <ion-icon name="settings-outline"></ion-icon>
                <strong><?=$functionController->locale('menu_item_settings')?></strong>
            </div>
        </a>
    <?php endif; ?>
</div>
<!-- * App Bottom Menu -->
<?php endif; ?>

<!-- App Sidebar -->
<div class="modal fade panelbox panelbox-left" id="sidebarPanel" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body p-0">
                <!-- profile box -->
                <div class="profileBox pt-2 pb-2">
                    <div class="image-wrapper">
                        <img src="/assets/img/sample/avatar/do-utilizador.png" alt="image" class="imaged w36">
                    </div>
                    <div class="in">
                        <strong><?=$_SESSION['name']?></strong>
                        <div class="text-muted"><?=$_SESSION['permission_name']?></div>
                    </div>
                    <a href="#" class="btn btn-link btn-icon sidebar-close" data-bs-dismiss="modal">
                        <ion-icon name="close-outline"></ion-icon>
                    </a>
                </div>

                <!-- menu -->
                <div class="listview-title mt-1">Menu</div>
                <ul class="listview flush transparent no-line image-listview">
                    <?php foreach ($menuController->menu_options() as $menu_option): ?>
                    <li>
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
                <!-- * menu -->

                <!-- others -->
                <div class="listview-title mt-1">Others</div>
                <ul class="listview flush transparent no-line image-listview">
                    <?php foreach ($menuController->other_options() as $menu_option): ?>
                        <li>
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
                <!-- * others -->

            </div>
        </div>
    </div>
</div>
<!-- * App Sidebar -->


<!-- iOS Add to Home Action Sheet -->
<div class="modal inset fade action-sheet ios-add-to-home" id="ios-add-to-home-screen" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$functionController->locale('install_app_android_message')?></h5>
                <a href="#" class="close-button" data-bs-dismiss="modal">
                    <ion-icon name="close"></ion-icon>
                </a>
            </div>
            <div class="modal-body">
                <div class="action-sheet-content text-center">
                    <div class="mb-1"><img src="/assets/img/icon/192x192.png" alt="image" class="imaged w64 mb-2">
                    </div>
                    <div>
                        <?=$functionController->locale('install_app_iphone')?>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-block" data-bs-dismiss="modal"><?=$functionController->locale('label_btn_cancel')?></button>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<!-- * iOS Add to Home Action Sheet -->


<!-- Android Add to Home Action Sheet -->
<div class="modal inset fade action-sheet android-add-to-home" id="android-add-to-home-screen" tabindex="-1"
     role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><?=$functionController->locale('install_app_android_message')?></h5>
                <a href="#" class="close-button" data-bs-dismiss="modal">
                    <ion-icon name="close"></ion-icon>
                </a>
            </div>
            <div class="modal-body">
                <div class="action-sheet-content text-center">
                    <div class="mb-1">
                        <img src="/assets/img/icon/192x192.png" alt="image" class="imaged w64 mb-2">
                    </div>
                    <div>
                        <?=$functionController->locale('install_app_android')?>
                    </div>
                    <div class="mt-2">
                        <button class="btn btn-primary btn-block" data-bs-dismiss="modal"><?=$functionController->locale('label_btn_cancel')?></button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- * Android Add to Home Action Sheet -->