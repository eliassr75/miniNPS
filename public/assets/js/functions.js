function dialog(response) {

    let dialogTheme = "";
    let icon = "";
    switch (response.status) {
        case "success":
            dialogTheme = "primary";
            icon = `<ion-icon name="checkmark-circle-outline"></ion-icon>`;
            break;
        case "warning":
            dialogTheme = "warning";
            icon = `<ion-icon name="warning-outline"></ion-icon>`
            break;
        case "error":
            dialogTheme = "danger";
            icon = `<ion-icon name="close-circle"></ion-icon>`;
            break;
        default:
            dialogTheme = "info";
            icon = `<ion-icon name="information-circle-outline"></ion-icon>`;
            break;
    }

    $('#dialog-container').html(`
        <div class="modal fade dialogbox" id="DialogIconed" data-bs-backdrop="static" tabIndex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-icon text-${dialogTheme}">
                        ${icon}
                    </div>
                    <div class="modal-header"></div>
                    <div class="modal-body">
                        ${response.message}
                    </div>
                    <div class="modal-footer">
                        <div class="btn-inline">
                            <a href="#" class="btn" data-bs-dismiss="modal">OK</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `)

    $("#DialogIconed").modal('toggle')

}
