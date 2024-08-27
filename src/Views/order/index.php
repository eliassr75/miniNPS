<?php include_once __DIR__ . '/../htmlInit.php'; ?>
<?php require_once __DIR__ . '/../htmlHead.php'; ?>
<?php require_once __DIR__ . '/../bodyContentInit.php'; ?>

<?php

use App\Controllers\FunctionController;
$functionController = new FunctionController();

?>

<div class="section my-2">
    <div class="row justify-content-center">
        <div class="col-lg-9 col-md-12">
            <div class="card">
                <div class="card-body" id="order">

                </div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/../bodyContentEnd.php'; ?>

<script src="/assets/js/custom_vars.js"></script>
<script src="/assets/js/orderController.js"></script>

<?php require_once __DIR__ . '/../htmlEnd.php';?>
