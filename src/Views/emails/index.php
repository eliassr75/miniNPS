<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Models\Registry;
use App\Controllers\FunctionController;
$functionController = new FunctionController();
$registry  = new Registry();
?>

<div class="section mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-12">
            <ul class="listview image-listview inset list my-2">

        <li class="w-100 align-items-center justify-content-between">
            <h3 class="m-2"><?=SUBTITLE_PAGE?></h3>
            <!--<a
                <?php if (!isset($url)): ?>
                    href="javascript:void(0)" onclick="actionForm('<?=$actionForm?>')"
                    data-bs-toggle="modal" data-bs-target="#actionSheetForm"
                <?php else: ?>
                    href="<?=$url?>"
                <?php endif; ?>
                    class="m-2 btn btn-primary">
                <ion-icon name="add-outline"></ion-icon>
                <?=$functionController->locale('label_btn_add')?>
            </a>-->
        </li>

        <li id="li-model">
            <?php
            $email_production = $registry->where('key', 'email_production')->first();
            if ($email_production) {
                $email_production->created_text = date('d/m/Y H:i', strtotime($email_production->created_at));
            }else{
                $email_production = new stdClass();
            }
            $email_production->title = $functionController->locale('label_production');
            $email_production->registry_key = 'email_production';
            ?>
            <a href="javascript:void(0)" onclick='actionForm("editEmails", <?=$email_production ? $functionController->parseObjectToJson($email_production) : []?>)'
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>
                        <?=$email_production->value ?? $functionController->locale('label_not_found')?>
                        <footer>
                            <?=$functionController->locale('label_production')?>
                        </footer>
                    </div>
                </div>

            </a>
        </li>
        <li id="li-model">
            <?php
            $email_fabric = $registry->where('key', 'email_fabric')->first();
            if ($email_fabric) {
                $email_fabric->created_text = date('d/m/Y H:i', strtotime($email_fabric->created_at));
            }else{
                $email_fabric = new stdClass();
                $email_fabric->title = $functionController->locale('label_fabric');
            }
            $email_fabric->registry_key = 'email_fabric';
            ?>
            <a href="javascript:void(0)" onclick='actionForm("editEmails", <?=$email_fabric ? $functionController->parseObjectToJson($email_fabric) : []?>)'
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>
                        <?=$email_fabric->value ?? $functionController->locale('label_not_found')?>
                        <footer>
                            <?=$functionController->locale('label_fabric')?>
                        </footer>
                    </div>
                </div>

            </a>
        </li>
        <li id="li-model">
            <?php
            $email_finance = $registry->where('key', 'email_finance')->first();
            if ($email_finance) {
                $email_finance->created_text = date('d/m/Y H:i', strtotime($email_finance->created_at));
            }else{
                $email_finance = new stdClass();
            }
            $email_finance->title = $functionController->locale('label_finance');
            $email_finance->registry_key = 'email_finance';
            ?>
            <a href="javascript:void(0)" onclick='actionForm("editEmails", <?=$email_finance ? $functionController->parseObjectToJson($email_finance) : []?>)'
               data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                <div class="icon-box bg-primary">
                    <ion-icon name="mail-outline"></ion-icon>
                </div>
                <div class="in">
                    <div>
                        <?=$email_finance->value ?? $functionController->locale('label_not_found')?>
                        <footer>
                            <?=$functionController->locale('label_finance')?>
                        </footer>
                    </div>
                </div>

            </a>
        </li>
    </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
