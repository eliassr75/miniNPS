<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\OrderFinance;
use App\Models\Orders;
use App\Models\OrdersItems;
use App\Models\OrderStatus;
use App\Models\Product;
use App\Models\Registry;
use App\Models\SubCategory;
use App\Models\User;
use Doctrine\Inflector\Rules\Transformation;
use Exception;
use TCPDF;

class OrdersController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('orders_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));

        $orders = Orders::orderBy('orders.id', 'desc')
        ->select(
            'orders.*',
            'client.name as client_name',
            'users.name as user_name',
            'type_status_orders.name as type_status_name',
            'type_status_orders.id as type_status_id',
            'type_status_orders.color as type_status_color',
            'type_status_finance.id as type_status_finance_id',
            'type_status_finance.name as type_status_finance_name',
            'type_status_finance.color as type_status_finance_color',
        )
        ->join('client', 'client.id', '=', 'orders.client_id')
        ->join('users', 'users.id', '=', 'orders.user_id')
        ->join('type_status_orders', 'type_status_orders.id', '=', 'orders.status_id')
        ->join('type_status_finance', 'type_status_finance.id', '=', 'orders.finance_id');

        if(in_array($_SESSION['permission_id'], [3])):
            $orders = $orders->where('orders.user_id', $_SESSION['id']);
        endif;
        $orders = $orders->get();

        $orders_array = [];
        foreach ($orders as $order) {
            $order->str_created = date('d/m/Y H:i', strtotime($order->created_at));
            $order->total_items = count($order->items()->get());
            $orders_array[] = $order;
        }

        $this->render('orders', [
            'orders' => $orders_array,
            'button' => 'add',
            'url' => '/order/new/'
        ]);
    }

    public function sendEmailNotification($email, $subject, $body, $url = null, $accessLabel = null) {
        $functionController = new FunctionController();
        return $functionController->sendMail(
            $email,
            $subject,
            $body,
            $url,
            $accessLabel
        );
    }

    function generatePDF($order, $items, $showPrices = true) {
        // Create new PDF document
        $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('FixaVidros');

        $name = date('Ymd', strtotime($order->created_at)) . "_" .  $order->id;
        $pdf->SetTitle("Pedido {$name}");

        $pdf->SetSubject('Data de criação: ' . date('d/m/Y H:i:s', strtotime($order->created_at)));
        $pdf->SetKeywords('TCPDF, PDF, order, details');

        // Set default header data
        $logo = __DIR__ . '/../../public/assets/img/logo-fixa-new.png';
        $pdf->Image($logo, 10, 10, 50, 0, 'PNG', '', 'T', false, 300, '', false, false, 0, false, false, false);

        // Add title and subtitle
        $pdf->SetFont('helvetica', 'B', 20);
        $pdf->Cell(0, 15, 'Pedido', 0, 1, 'C', 0, '', 0, false, 'M', 'M');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 15, 'Data de criação: ' . date('d/m/Y', strtotime($order->created_at)), 0, 1, 'C', 0, '', 0, false, 'M', 'M');

        // Set default monospaced font
        $pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

        // Set margins
        $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
        $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
        $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

        // Set auto page breaks
        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        // Set image scale factor
        $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

        // Add a page
        $pdf->AddPage();

        // Add order information
        $html = '<h1>Detalhes do Pedido</h1>';
        $html .= '<p>Id do Pedido: ' . $order->id . '</p>';
        if ($showPrices) {
            $html .= '<p>Valor total: ' . $this->formatCurrencyBR($order->total_price) . '</p>';
        }
        $html .= '<p>Cliente: ' . htmlspecialchars($order->client_name) . '</p>';
        $html .= '<h2>Itens do Pedido</h2>';

        // Add items table
        $html .= '<table border="1" cellpadding="5" cellspacing="0">';
        $html .= '<tr>';
        $html .= '<th>Produto</th>';
        $html .= '<th>Quantidade</th>';
        $html .= '<th>Dimensões</th>';
        if ($showPrices) {
            $html .= '<th>Preço</th>';
        }
        $html .= '</tr>';

        foreach ($items as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['quantity']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['width']) . ' x ' . htmlspecialchars($item['height']) . '</td>';
            if ($showPrices) {
                $html .= '<td>' . htmlspecialchars($this->formatCurrencyBR($item['price'])) . '</td>';
            }
            $html .= '</tr>';
        }

        $html .= '</table>';

        // Print text using writeHTMLCell()
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF document
        $file =  __DIR__ . '/../../public/assets/docs/orders_' . $name . '.pdf';
        $pdf->Output($file, 'F');
    }

    public function generateOrderItemsTable($items, $prices=true) {

        $functionController = new FunctionController();

        $html = '<table border="1" cellpadding="5" cellspacing="0" style="font-size: 11px; width: 100%">';
        $html .= '<tr>';
        $html .= '<th>Produto</th>';
        $html .= '<th>Tipo</th>';
        $html .= '<th>Quantidade</th>';
        $html .= '<th>Dimensões</th>';

        if($prices):
            $html .= '<th>Preço</th>';
        endif;
        $html .= '</tr>';

        foreach ($items as $item) {
            $html .= '<tr>';
            $html .= '<td>' . htmlspecialchars($item['name']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['glass_type_id'] == 1 ? 'COMUM' : 'TEMPERADO') . '</td>';
            $html .= '<td>' . htmlspecialchars($item['quantity']) . '</td>';
            $html .= '<td>' . htmlspecialchars($item['width']) . ' x ' . htmlspecialchars($item['height']) . '</td>';
            if($prices):
                $html .= '<td>' . htmlspecialchars($functionController->formatCurrencyBR($item['price'])) . '</td>';
            endif;
            $html .= '</tr>';
        }

        $html .= '</table>';
        return $html;
    }

    function formatCurrencyBR($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public function prepareOrderData($orderId)
    {
        $functionController = new FunctionController();
        $orderStatus = [];
        $orderFinance = [];
        $existsOrder = [];
        if($orderId){
            $orderStatus = OrderStatus::orderBy('id', 'asc')->get();
            $orderFinance = OrderFinance::orderBy('id', 'asc')->get();
            $order = Orders::find($orderId);
            $orderItems = $order->items()->where('active', true)->get();
            $existsOrder['order'] = $order;
            $orderItemsArray = [];
            foreach ($orderItems as $orderItem) {
                $orderItem->client_id = $order->client_id;
                $orderItemsArray[] = $orderItem;
            }

            $existsOrder['items'] = $orderItemsArray;
        }

        $clients = Client::where('active', true)->orderBy('id', 'desc')->get();
        $clients_array = [];
        foreach ($clients as $client) {
            $client->str_created = date('d/m/Y H:i', strtotime($client->created_at));
            $clients_array[] = $client;
        }

        $settingsController = new SettingsController();
        $settingsController->only_return = true;

        $categories = Category::where('active', true)->get();
        $subCategorias = [];
        $products_array = [];

        foreach ($categories as $category) {
            $category->thickness = $category->thickness()->get();
            $subcategories = SubCategory::where('sub_category.active', true)
                ->where('sub_category.category_id', $category->id)
                ->select(
                    'sub_category.*',
                    'glass_type.name as glass_type_name'
                )
                ->join('glass_type', 'glass_type.id', '=', 'sub_category.glass_type_id')
                ->get();

            $subCategorias[$category->id] = $subcategories;
            $products_array[$category->id] = [];
            foreach ($subcategories as $subcategory) {

                $products_array[$category->id][] = [
                    "name" => $category->name . " " . $subcategory->name,
                    "str_created" => date('d/m/Y H:i', strtotime($subcategory->created_at)),
                    "custom_name" => $subcategory->additional_name,
                    "glass_type_name" => $subcategory->glass_type_name,
                    "glass_type_id" => $subcategory->glass_type_id,
                    "category_id" => $category->id,
                    "sub_category_id" => $subcategory->id,
                    "obs" => null,
                    "category_name" => $category->name,
                    "sub_category_name" => $subcategory->name,
                    "image" => $subcategory->image,
                ];
            }
        }

        $logs = [];
        if($orderId):

            $order = Orders::where('orders.id', $orderId)
                ->select(
                    'orders.*',
                    'client.name as client_name',
                    'users.name as user_name'
                )
                ->join('client', 'client.id', '=', 'orders.client_id')
                ->join('users', 'users.id', '=', 'orders.user_id')
                ->first();
            $order->str_created = date('d/m/Y H:i', strtotime($order->created_at));

            foreach ($order->log_entry()->orderBy('id', 'desc')->get() as $log) {
                $log->str_created = date('d/m/Y H:i', strtotime($log->created));
                $logs[] = $log;
            }

        else:
            $order = new Orders();
        endif;

        $data = [
            'existsOrder' => $existsOrder,
            'readOnly' => !in_array($_SESSION['permission_id'], [1, 2, 3]),
            'showPrice' => in_array($_SESSION['permission_id'], [1, 2, 3]),
            'min_date' => date('Y-m-d'),
            'order' => $order,
            'logs' => $logs,
            'orderStatus' => $orderStatus,
            'orderFinance' => $orderFinance,
            'categories' => $categories,
            'subCategorias' => $subCategorias,
            'products' => $products_array,
            'clients_array' => $clients_array,
            'thickness' => $settingsController->glass_thickness(),
            'types' => $settingsController->glass_type(),
            'colors' => $settingsController->glass_colors(),
            'clearances' => $settingsController->glass_clearances(),
            'finish' => $settingsController->glass_finish(),
        ];

        $functionController->exportVarsToJS($data);
    }

    public function getOrder($orderId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('orders_page', true);

        $this->prepareOrderData($orderId);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_orders'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_orders'));
        $this->render('order', ['button' => "None"]);
    }

    public function newOrder()
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $response = $functionController->baseResponse();
        $data = $functionController->postStatement($_POST);

        $order = new Orders();
        $order->client_id = $data->client_id;
        $order->user_id = $_SESSION['id'];
        $order->total_price = $data->total_price;
        $order->obs_client = $data->obs_client;
        $order->date_delivery = $data->date_delivery;
        $order->save();

        $order->setLog('Orders', "Pedido $order->id adicionado por {$_SESSION['username']} ({$_SESSION['id']})", [], $order);
        $items = [];
        foreach ($data->items as $key => $value) {
            foreach ($value as $item) {
                $base = [];
                $base['order_id'] = $order->id;
                $base['name'] = $item->name;
                $base['category_id'] = $item->category_id;
                $base['sub_category_id'] = $item->sub_category_id;
                if (intval($item->product_id)):
                    $base['product_id'] = $item->product_id;
                endif;
                $base['glass_thickness_id'] = $item->glass_thickness_id;
                $base['glass_color_id'] = $item->glass_color_id;
                $base['glass_finish_id'] = $item->glass_finish_id;
                $base['glass_clearances_id'] = $item->glass_clearances_id;
                $base['glass_type_id'] = $item->glass_type_id;
                $base['quantity'] = $item->quantity;
                $base['width'] = $item->width;
                $base['height'] = $item->height;
                $base['obs_factory'] = $item->obs_factory;
                $base['obs_client'] = $item->obs_client;
                $base['obs_tempera'] = $item->obs_tempera;
                $base['price'] = $item->price;

                $items[] = $base;
                $order->setLog('OrdersItems', "Produto $item->name adicionado por {$_SESSION['username']} ({$_SESSION['id']})", [], $base);
            }
        }

        $order = Orders::find($order->id);
        try{
            $order->items()->createMany($items);
            $response->message = $functionController->locale('register_success_created');
        }catch (Exception $e){
            $response->message = $e->getMessage();
        }

        $response->status = "success";
        $response->dialog = true;
        $response->url = "/order/{$order->id}";
        $response->spinner = true;
        $functionController->sendResponse($response, $status_code);
    }

    public function updateOrder($orderId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;
        $send_email = false;

        try{

            $data = $functionController->customPutStatement();
            $response = $functionController->baseResponse();

            $order = Orders::find($orderId);
            $oldOrder = Orders::find($orderId);

            $order->status_id = $data->status_id;
            $order->finance_id = $data->finance_id;
            $order->total_price = $data->total_price;
            $order->client_id = $data->client_id;
            $order->obs_client = $data->obs_client;
            $order->date_delivery = $data->date_delivery;

            if (!empty($data->ids_to_remove)) {
                $ids = explode(',', $data->ids_to_remove);
                foreach ($ids as $id) {
                    $item = OrdersItems::find($id);
                    $order->setLog('OrdersItems', "Produto $item->name removido por {$_SESSION['username']} ({$_SESSION['id']})", $item, []);
                }
                $order->items()->whereIn('id', $ids)->update(['active' => false]);
            }

            $hasChangesOrder = false;
            foreach ($data as $key => $value) {
                if ($key != "total_price" and $key != "items" and isset($oldOrder->$key) and $oldOrder->$key != $value) {
                    $hasChangesOrder = true;
                    break;
                }
            }
            $order->save();
            if ($hasChangesOrder > 0) {
                $order->setLog('Orders', "Pedido $order->id modificado por {$_SESSION['username']} ({$_SESSION['id']})", $oldOrder, $order);
            }

            $items = [];
            $singleProducts = [];
            $temperedProducts = [];

            $needsCreate = [];
            foreach ($data->items as $key => $value) {
                foreach ($value as $item) {
                    $base = [];
                    $base['category_id'] = $item->category_id;
                    $base['name'] = $item->name;
                    $base['sub_category_id'] = $item->sub_category_id;
                    if (intval($item->product_id)):
                        $base['product_id'] = $item->product_id;
                    endif;
                    $base['glass_thickness_id'] = $item->glass_thickness_id;
                    $base['glass_color_id'] = $item->glass_color_id;
                    $base['glass_finish_id'] = $item->glass_finish_id;
                    $base['glass_clearances_id'] = $item->glass_clearances_id;
                    $base['glass_type_id'] = $item->glass_type_id;
                    $base['quantity'] = $item->quantity;
                    $base['width'] = $item->width;
                    $base['height'] = $item->height;
                    $base['obs_factory'] = $item->obs_factory;
                    $base['obs_client'] = $item->obs_client;
                    $base['obs_tempera'] = $item->obs_tempera;
                    $base['price'] = $item->price;

                    $items[] = $base;
                    if($item->glass_type_id == 1):
                        $singleProducts[] = $base;
                    else:
                        $temperedProducts[] = $base;
                    endif;

                    if (!$order->items()->where('id', $item->id)->exists()) {

                        $needsCreate[] = $base;
                        $order->setLog('OrdersItems', "Produto $item->name adicionado por {$_SESSION['username']} ({$_SESSION['id']})", [], $base);
                    }else{
                        $oldItem = $order->items()->where('id', $item->id)->first();
                        $hasChanges = false;
                        foreach ($item as $key => $value) {
                            if (isset($oldItem->$key) and $oldItem->$key != $value) {
                                $hasChanges = true;
                                break;
                            }
                        }
                        if ($hasChanges) {
                            OrdersItems::where('id', $item->id)->update($base);
                            $newItem = $order->items()->where('id', $item->id)->first();
                            $order->setLog('OrdersItems', "Produto $item->name modificado por {$_SESSION['username']} ({$_SESSION['id']})", $oldItem, $newItem);
                        }
                    }

                }
            }

            $response->message = $functionController->locale('register_success_update');
            if ($needsCreate) {
                $order->items()->createMany($needsCreate);
            }

            if ($hasChangesOrder > 0 and $order->status_id == 2){

                $client = Client::find($order->client_id);
                $emailFinance = "
                    <span>Id do Pedido: {$order->id}</span> <br>
                    <span>Valor total: {$functionController->formatCurrencyBR($order->total_price)}</span> <br>
                    <span>Cliente: {$client->id}-{$client->name}</span> <br>
                    <span>O.C.: {$order->obs_client}</span> <br>
                    <span>Itens do Pedido:</span> <br>
                    " . $this->generateOrderItemsTable($items) . "
                ";

                $email_finance = Registry::where('key', 'email_finance')->first();
                $email_fabric = Registry::where('key', 'email_fabric')->first();
                $email_production = Registry::where('key', 'email_production')->first();

                $this->sendEmailNotification(
                    $email_finance->value,
                    $functionController->locale('label_new_order_added'),
                    $emailFinance,
                    "{$functionController->generateCurrentUrl("/order/{$order->id}/")}",
                    "{$functionController->locale('access')} {$functionController->locale('menu_item_orders')}"
                );

                if(count($temperedProducts)):
                    $emailFabric = "
                        <span>Id do Pedido: {$order->id}</span> <br>
                        <span>Cliente: {$client->id}-{$client->name}</span> <br>
                        <span>O.C.: {$order->obs_client}</span> <br>
                        <span>Itens do Pedido:</span> <br>
                        " . $this->generateOrderItemsTable($temperedProducts, false) . "
                    ";
                    $this->sendEmailNotification(
                        $email_fabric->value,
                        $functionController->locale('label_new_order_added'),
                        $emailFabric
                    );
                endif;

                if(count($singleProducts)):
                    $emailProduction = "
                        <span>Id do Pedido: {$order->id}</span> <br>
                        <span>Cliente: {$client->id}-{$client->name}</span> <br>
                        <span>O.C.: {$order->obs_client}</span> <br>
                        <span>Itens do Pedido:</span> <br>
                        " . $this->generateOrderItemsTable($singleProducts, false) . "
                    ";
                    $this->sendEmailNotification(
                        $email_production->value,
                        $functionController->locale('label_new_order_added'),
                        $emailProduction,
                        "{$functionController->generateCurrentUrl("/order/{$order->id}/")}",
                        "{$functionController->locale('access')} {$functionController->locale('menu_item_orders')}"
                    );
                endif;
            }

            $response->status = "success";
            $response->reload = true;
            $response->dialog = true;
            $response->spinner = true;

            $functionController->sendResponse($response, $status_code);

        }catch (Exception $e){
            $response->message = $e->getMessage();
            $response->dialog = true;
            $response->spinner = false;
            $functionController->sendResponse($response, $status_code);
            return;
        }
    }

    public function changeOrder($orderId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $response = $functionController->baseResponse();
        $status_code = 200;

        $orderModel = new Orders();
        $order_search = $orderModel->find($orderId);

        $order_search->status = "Cancelado";
        $user = User::find($_SESSION['id']);
        $user->setLog('Orders', "O status do pedido {$order_search->id} foi modificado para " . ($order_search->status) . " - {$_SESSION['name']}");
        $order_search->save();

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->dialog = true;
        $functionController->sendResponse($response, $status_code);
    }
}