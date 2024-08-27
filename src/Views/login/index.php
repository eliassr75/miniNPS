<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<div class="row">
    <div class="ms-auto col-lg-4 col-md-6 col-12 me-auto">

        <div class="section mt-2 text-center">
            <img src="/assets/img/logo-fixa.png" class="w-75" alt="">
        </div>
        <div class="section mb-5 p-2">

            <form data-method="POST" data-action="<?=$route?>" data-ajax="default" data-callback="">

                <div class="card">
                    <div class="card-body pb-1">
                        <div class="form-group basic animated">
                            <div class="input-wrapper">
                                <label class="label" for="username"><?=$functionController->locale('input_email')?></label>
                                <input type="email" class="form-control" id="username" name="username"
                                       placeholder="<?=$functionController->locale('input_email')?>"
                                       autocomplete="username"
                                       required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <div class="form-group basic animated">
                            <div class="input-wrapper">
                                <label class="label" for="password1"><?=$functionController->locale('input_password')?></label>
                                <input type="password" class="form-control" id="password" name="password"
                                autocomplete="new-password" placeholder="<?=$functionController->locale('input_password')?>" required>
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-links mt-2">
                    <div>
                        <a href="/new-account/"><?=$functionController->locale('create_account')?></a>
                    </div>
                    <div>
                        <a href="/forget-password/" class="text-muted">
                            <?=$functionController->locale('forget_password')?>
                        </a>
                    </div>
                </div>

                <div class="mt-2 transparent">
                    <button type="submit" class="btn btn-primary btn-block btn-lg btn-submit">
                        <?=$functionController->locale('access')?>
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>
<?php require_once __DIR__ . '/../htmlEnd.php';?>
