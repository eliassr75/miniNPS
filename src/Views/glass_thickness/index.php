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

                <li class="w-100 align-items-center justify-content-between">
                    <h3 class="m-2"><?=SUBTITLE_PAGE?></h3>
                    <a
                        <?php if (!isset($url)): ?>
                            href="javascript:void(0)" onclick="actionForm('<?=$actionForm?>')"
                            data-bs-toggle="modal" data-bs-target="#actionSheetForm"
                        <?php else: ?>
                            href="<?=$url?>"
                        <?php endif; ?>
                            class="m-2 btn btn-primary">
                        <ion-icon name="add-outline"></ion-icon>
                        <?=$functionController->locale('label_btn_add')?>
                    </a>
                </li>

                <?php if(count($thickness)): foreach ($thickness as $item):

                    $item->units = $functionController->glassUnits();
                    $item->created_text = date('d/m/Y H:i', strtotime($item->created_at));
                    $item->locale = $_SESSION['user_language'];

                    ?>

                    <li id="li-model">
                        <a href="javascript:void(0)" onclick='actionForm("editThickness", <?=$functionController->parseObjectToJson($item)?>)'
                           data-bs-toggle="modal" data-bs-target="#actionSheetForm" class="item">
                            <div class="icon-box bg-<?=$item->active ? "primary" : "danger"?>">
                                <ion-icon name="layers-outline"></ion-icon>
                            </div>
                            <div class="in">
                                <?=$item->name?> <?=$item->type?>
                                <div>
                                    <?php if($item->price): ?>
                                        <span class="badge badge-primary">
                                            R$ <?=$item->price?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </li>
                <?php endforeach;  else: ?>
                    <div class="alert alert-primary" role="alert">
                        <?= $functionController->locale('not_found_results') ?>
                    </div>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script>
    window.units = <?=$functionController->parseObjectToJson($functionController->glassUnits())?>;
    window.locale = "<?=$_SESSION['user_language']?>";
</script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
