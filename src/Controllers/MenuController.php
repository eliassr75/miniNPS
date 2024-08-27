<?php

namespace App\Controllers;

use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

class MenuController extends BaseController
{

    public function menu_options()
    {

        $functionController = new FunctionController();
        $options = [
            [
                "name" => $functionController->locale('menu_item_client'),
                "permissions" => [1, 2, 3],
                "url" => '/clients/',
                "icon" => 'people-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_orders'),
                "permissions" => [1, 2, 3, 4],
                "url" => '/orders/',
                "icon" => 'document-text-outline',
                "badge" => false
            ],
            /*
            [
                "name" => $functionController->locale('menu_item_products'),
                "permissions" => [1, 2, 3],
                "url" => '/products/',
                "icon" => 'apps-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_financial'),
                "permissions" => [1, 2, 3],
                "url" => '/financial/',
                "icon" => 'card-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_report'),
                "permissions" => [1, 2, 3, 4],
                "url" => '/reports/',
                "icon" => 'stats-chart-outline',
                "badge" => false
            ],*/
        ];

        $user_permission_id = $_SESSION['permission_id'];
        $obj_menu_options = [];
        foreach ($options as $option) {

            if (in_array($user_permission_id, $option['permissions'])):

                $obj_menu_options[] = $option;

            endif;

        }

        return $functionController->parseJsonToObject($obj_menu_options);

    }

    public function other_options()
    {

        $functionController = new FunctionController();
        $options = [
            [
                "name" => $functionController->locale('menu_item_settings'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/',
                "icon" => 'settings-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_users'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/users/',
                "icon" => 'people-circle-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_support'),
                "permissions" => [1, 2, 3],
                "individual" => false,
                "url" => 'http://api.whatsapp.com/send?phone=5554993276132',
                "icon" => 'chatbubble-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_logout'),
                "permissions" => [1, 2, 3, 4],
                "individual" => false,
                "url" => '/logout/',
                "icon" => 'exit-outline',
                "badge" => false
            ],
        ];

        $user_permission_id = $_SESSION['permission_id'];
        $obj_menu_options = [];
        foreach ($options as $option) {

            if (in_array($user_permission_id, $option['permissions'])):

                $obj_menu_options[] = $option;

            endif;

        }

        return $functionController->parseJsonToObject($obj_menu_options);

    }
}