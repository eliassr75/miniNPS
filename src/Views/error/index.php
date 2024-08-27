<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<div class="section mt-2 text-center">
    <img src="/assets/img/logo-fixa.png" class="w-75" alt="">
</div>

<div class="section align-items-center mt-auto">
    <div class="splash-page mt-5 mb-5">

        <hr>
        <h1><?=$code?></h1>
        <h2 class="mb-2"><?=$functionController->locale("{$code}_error")?>!</h2>
        <p>
            <?=$functionController->locale("{$code}_message")?>
        </p>
    </div>
</div>

<div class="fixed-bar">
    <div class="row justify-content-center">
        <div class="col-md-6 col-12">
            <a href="#" class="btn btn-lg btn-primary btn-block goBack" onclick="window.location.href='/dashboard/'">
                <?=$functionController->locale('go_back')?>
            </a>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
