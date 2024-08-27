

    </div>
    <!-- * App Capsule -->

    <div class="modal fade action-sheet" id="actionSheetForm" tabindex="-1" style="display: none;" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="global-action-sheet-title">--</h5>
                </div>
                <div class="modal-body">
                    <div class="action-sheet-content" id="global-action-sheet-content">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        if(isset($login) && $login === false){
            require_once 'additionalContent.php';
        }
    ?>

    <?php require_once 'htmlScripts.php'; ?>

</body>