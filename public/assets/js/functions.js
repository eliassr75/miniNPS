let configDataTable = {
    stateSave: true,
    responsive: true,
    language: {
        searchPlaceholder: 'Faça uma pesquisa nesta página',
        zeroRecords: "Não encontramos resultados...",
        sSearch: '',
        sLengthMenu: '_MENU_',
        sLength: 'dataTables_length',
        info: 'Total de Registros: _TOTAL_',
        infoFiltered: '(Filtrado de _MAX_ resultados)',
        infoEmpty: "Total de Registros: _TOTAL_",
        oPaginate: {
            sFirst: '<i class="bi bi-arrow-left-circle"></i>',
            sPrevious: '<i class="bi bi-arrow-left-circle"></i>',
            sNext: '<i class="bi bi-arrow-right-circle"></i>',
            sLast: '<i class="bi bi-arrow-right-circle"></i>'
        }
    },
    order: false,
    lengthChange: true,
    autoWidth: true,
    paging: false
}


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
