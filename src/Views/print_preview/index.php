<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <script type="text/javascript" src="/assets/js/qr_code/jquery.min.js"></script>
    <script type="text/javascript" src="/assets/js/qr_code/qrcode.js"></script>
    <title>Impressão de Etiquetas</title>
    <style>

        <?php switch ($routeName){
            case 'products':?>
            @media print {
                body {
                    margin: 0;
                    padding: 0;
                    width: 100%;
                }

                .label {
                    width: <?=$print->width?>mm;
                    height: <?=$print->height?>mm;
                    max-width: <?=$print->width?>mm !important;
                    max-height: <?=$print->height?>mm !important;
                    padding-right: 5mm;
                    padding-left: 5mm;
                    border: 1px solid #FFFFFF;
                    page-break-inside: avoid;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .label .text {
                    font-size: 12pt;
                }

                .no-print {
                    display: none;
                }

                #qrcode {
                    margin-left: auto;
                }

                p {
                    margin: 0;
                    padding: 0;
                }
            }

            .label {
                display: flex;
                justify-content: space-between;
                align-items: center;
            }
            <?php break;
        case 'order': ?>

        body {
            font-family: Arial, sans-serif;
            margin: 20px;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .header div {
            flex: 1;
        }
        .header .client-info {
            text-align: left;
        }
        .header .other-info {
            text-align: right;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        .total {
            text-align: right;
            font-weight: bold;
            margin-top: 10px;
        }

        <?php break;

        }?>

        .hidden {
            display: none;
        }
    </style>
</head>
<body>
<div style="margin-bottom: 2mm; width: 100%; display: flex; justify-content: center;">
    <button id="printButton" onclick="f_print()">
        Imprimir Etiqueta
    </button>
</div>



<?php switch ($routeName){
    case 'products': ?>

<div id="orderItems">

</div>

<?php break;
case 'order': ?>

    <div class="container">
        <div class="header">
            <div class="client-info">
                <strong>Cliente:</strong> <span id="clientName"></span>
            </div>
            <div class="other-info">
                <strong>Previsão de Entrega:</strong> <span id="orderDate"></span><br>
                <strong>Pedido:</strong> <span id="orderId"></span><br>
                <strong>O.C.:</strong> <span id="obs_client"></span>
            </div>
        </div>

        <table>
            <thead>
            <tr>
                <th>Quantidades</th>
                <th>Descrição</th>
                <th>Dimensões</th>
                <th>Valor Total</th>
            </tr>
            </thead>
            <tbody id="orderItems">
            <!-- Itens do pedido serão inseridos aqui -->
            </tbody>
        </table>

        <div class="total">
            Total do Pedido: <span id="orderTotal"></span>
        </div>
        <div id="qrcode" style="height:30mm;"></div>
    </div>

<?php break;
}
?>
<script src="/assets/js/custom_vars.js"></script>
<script>

    function getItemsOrderList() {
        const storageKey = 'itemsOrderList';
        let storedData = localStorage.getItem(storageKey);
        return storedData ? JSON.parse(storedData) : [];
    }

    function formatDate(dateString) {
        const [year, month, day] = dateString.split('-');
        return `${day}/${month}/${year}`;
    }

    function formatCurrency(value) {
        return new Intl.NumberFormat('pt-BR', {
            style: 'currency',
            currency: 'BRL'
        }).format(value);
    }

    function f_print() {
        let btn = document.getElementById('printButton');
        btn.classList.add('hidden');
        window.print();
        btn.classList.remove('hidden');
    }

    function makeCode(qrcode) {
        qrcode.makeCode(`${window.location.host}<?= $urlQrCode ?>?redirect=true&url=<?= $urlQrCode ?>`);
    }

    const orderItems = getItemsOrderList();

    <?php switch ($routeName){
        case 'products':
        ?>

        function renderOrderItems() {
            const orderItemsContainer = document.getElementById('orderItems');
            orderItemsContainer.innerHTML = ''; // Limpa o container antes de adicionar novos itens

            let client_name = ""
            for (let item of clients_array){
                if (item.id === existsOrder.order.client_id){
                    client_name = `${item.id}-${item.name}`
                    break;
                }
            }

            orderItems.forEach(itemObj => {
                const item = Object.values(itemObj)[0];

                // Adiciona o HTML para o item
                orderItemsContainer.insertAdjacentHTML('beforeend', `
                <div class="label">
                    <div class="text">
                        <p style="font-size: 25px"><b>${client_name.toUpperCase()}</b></p>
                        <p style="margin-left: 5mm;">${item.name || 'Vidro Temperado'}</p>
                        <p style="margin-left: 5mm;">QTD: ${item.quantity || 0}</p>
                        <p style="margin-left: 5mm;">PED: ${existsOrder.order.id || 0}</p>
                        <p style="margin-left: 8mm;">O.C.: </p>
                        <p>${(item.obs_client ? item.obs_client.toUpperCase() : "") || (existsOrder.order.obs_client ? existsOrder.order.obs_client.toUpperCase() : "")}</p>
                        <br>
                        <p>ENT: ${formatDate(existsOrder.order.date_delivery)}</p>
                        <p style="margin-left: 10mm; font-size: 25px"><b>(${item.width}X${item.height})</b></p>
                    </div>
                    <div>
                        <div id="qrcode-${item.id}" style="height: 65mm; width: 35vw;"></div>
                    </div>
                </div>
            `);

                // Gera o QR code
                const qrcode = new QRCode(document.getElementById(`qrcode-${item.id}`), {
                    width: 100,
                    height: 100,
                    useSVG: true,
                    correctLevel: QRCode.CorrectLevel.L
                });
                makeCode(qrcode);
            });
        }

        window.onload = renderOrderItems;

    <?php break;
    case 'order': ?>

        function renderOrderSummary() {


            const orderItemsContainer = document.getElementById('orderItems');
            const orderTotalContainer = document.getElementById('orderTotal');
            const clientName = document.getElementById('clientName');
            const orderDate = document.getElementById('orderDate');
            const orderId = document.getElementById('orderId');
            const obs_client = document.getElementById('obs_client');

            let total = 0;
            let itemsTemplate = '';

            let client_name = ""
            for (let item of clients_array){
                if (item.id === existsOrder.order.client_id){
                    client_name = `${item.id}-${item.name}`
                    break;
                }
            }

            clientName.textContent = client_name;
            orderDate.textContent = formatDate(existsOrder.order.date_delivery);
            orderId.textContent = existsOrder.order.id;
            obs_client.textContent = existsOrder.order ? existsOrder.order.obs_client : "" ;

            orderItems.forEach(itemObj => {
                const item = Object.values(itemObj)[0];
                let itemTotal = parseFloat(item.price);
                total += itemTotal;

                itemsTemplate += `
                        <tr>
                            <td>${item.quantity}</td>
                            <td>${item.name}</td>
                            <td>${item.width} X ${item.height}</td>
                            <td>${formatCurrency(itemTotal)}</td>
                        </tr>
                    `;
            });

            orderItemsContainer.innerHTML = itemsTemplate;
            orderTotalContainer.textContent = formatCurrency(total);
        }

        const qrcode = new QRCode(document.getElementById(`qrcode`), {
            width: 100,
            height: 100,
            useSVG: true,
            correctLevel: QRCode.CorrectLevel.L
        });
        makeCode(qrcode);

        window.onload = renderOrderSummary;

    <?php  break;
    }?>

</script>
</body>
</html>
