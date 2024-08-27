<?php

namespace App\Controllers;

use App\Models\Orders;
use App\Models\OrderStatus;
use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

define('TITLE_PAGE', 'Fixa Vidros - Dashboard');

class DashboardController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);

        if(in_array($_SESSION['permission_id'], [4])):
            header('Location: /orders/');
        endif;

        $orderStatus = Orders::orderBy('orders.status_id', 'asc')
            ->orderBy('orders.id', 'desc')
            ->select(
                'orders.*',
                'client.name as client_name',
                'users.name as user_name',
                'type_status_orders.name as type_status_name',
                'type_status_orders.id as type_status_id',
                'type_status_orders.color as type_status_color',
                'type_status_finance.color as type_finance_color',
                'type_status_finance.id as type_status_finance_id',
                'type_status_finance.name as type_status_finance_name',
            )
            ->join('client', 'client.id', '=', 'orders.client_id')
            ->join('users', 'users.id', '=', 'orders.user_id')
            ->join('type_status_orders', 'type_status_orders.id', '=', 'orders.status_id')
            ->join('type_status_finance', 'type_status_finance.id', '=', 'orders.finance_id');

        if(in_array($_SESSION['permission_id'], [3])):
            $orderStatus = $orderStatus->where('orders.user_id', $_SESSION['id']);
        endif;
        $orderStatus = $orderStatus->limit(10)->get();

        $this->render('dashboard', [
            'orderStatus' => $orderStatus,
        ]);
    }
}