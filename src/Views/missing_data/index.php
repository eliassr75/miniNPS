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

            <form data-method="PUT" data-action="<?=$route?>" data-ajax="default" data-callback="">

                <div class="card">
                    <div class="card-body pb-1">

                        <div class="w-100 text-center pageTitle">
                            <?=$functionController->locale('missing_data')?>
                        </div>

                        <?php $requires = json_decode(json_encode($requires, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)); ?>

                        <?php foreach ($requires as $require): ?>

                        <?php if ($require->name == "zip_code"): ?>
                            <br>
                            <div class="custom-alert my-2"></div>
                        <?php endif; ?>

                        <div class="form-group basic animated">
                            <div class="input-wrapper">
                                <label class="label" for="<?=$require->name?>"><?=$functionController->locale("input_{$require->name}")?></label>
                                <input type="<?=$require->type?>" class="form-control" autocomplete="n-password" id="<?=$require->name?>" name="<?=$require->name?>" placeholder="<?=$functionController->locale("input_{$require->name}")?>">
                                <i class="clear-input">
                                    <ion-icon name="close-circle"></ion-icon>
                                </i>
                            </div>
                        </div>

                        <?php endforeach; ?>

                    </div>
                </div>

                <div class="mt-2 transparent">
                    <button type="submit" class="btn btn-primary btn-block btn-lg btn-submit">
                        <?=$functionController->locale('continue')?>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>


<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>

    $(document).ready(() => {

        <?php foreach ($requires as $require): ?>
            <?php if ($require->name == "zip_code"): ?>
                $("#<?=$require->name?>").mask("00000-000").on("keyup", function (){
                    get_cep(this.value)
                })
            <?php else: ?>
                <?php if ($require->mask): ?>
                    $("#<?=$require->name?>").mask("<?=$require->mask?>").attr('required', <?=$require->required ? "true" : "false"?>)
                <?php else: ?>
                    $("#<?=$require->name?>").attr('required', <?=$require->required ? "true" : "false"?>)
                <?php endif; ?>
            <?php endif; ?>
        <?php endforeach; ?>
    })

</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
