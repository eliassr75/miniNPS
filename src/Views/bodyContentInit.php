<body class="">

    <!-- loader -->
    <div id="loader">
        <img src="/assets/img/loading-icon.png" alt="icon" class="loading-icon">
    </div>
    <!-- * loader -->

    <div class="w-100" id="progressContainer">
        <div class="progress rounded-0 mb-0" role="progressbar" id="progressDiv" aria-label="progressDiv" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
            <div class="progress-bar" id="progressBar" style="width: 0%"></div>
        </div>
    </div>

    <?php
        if(isset($login) && $login === false) {
            require_once 'htmlHeader.php';
        }
    ?>

    <div id="global-custom-alert" class="my-2 px-2"></div>

    <!-- App Capsule -->
    <div id="appCapsule">

        <div id="toast-container"></div>
        <div id="dialog-container"></div>
