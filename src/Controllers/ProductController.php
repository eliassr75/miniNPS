<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\Client;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\Permissions;
use App\Models\Product;
use App\Models\SubCategory;
use App\Models\User;
use Doctrine\Inflector\Rules\Transformation;
use Exception;

class ProductController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('products_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_products'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_products'));

        $products = Product::orderBy('products.id', 'desc')
        ->select(
            'products.*',
            'category.name as category_name',
            'category.id as category_id',
            'category.active as category_active',
            'glass_type.name as glass_type_name',
            'glass_type.id as glass_type_id',
            'glass_type.active as glass_type_active',
            'sub_category.name as sub_category_name',
            'sub_category.id as sub_category_id',
            'sub_category.active as sub_category_active',
            'sub_category.image as image'
        )
        ->join('category', 'category.id', '=', 'products.category_id')
        ->join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
        ->join('glass_type', 'glass_type.id', '=', 'products.glass_type_id')
        ->get();
        $products_array = [];
        foreach ($products as $product) {
            $product->str_created = date('d/m/Y H:i', strtotime($product->created_at));
            $products_array[] = $product;
        }

        $this->render('products', [
            'products' => $products_array,
            'button' => 'None',
        ]);
    }

    public function getProduct($productId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('products_page', true);

        $defaultThickness = GlassThickness::where('glass_type_id', null)
            ->where('active', true)
            ->where('products_id', null)
            ->where('category_id', null)
            ->orderBy('id', 'desc')->get();
        
        $types = GlassType::where('active', true)->get();
        $categories = Category::where('active', true)->get();
        $category = false;

        if($productId):

            $product = Product::where('products.id', $productId)->
            select(
                    'products.*',
                    'category.name as category_name',
                    'category.id as category_id',
                    'category.active as category_active',
                    'glass_type.name as glass_type_name',
                    'glass_type.id as glass_type_id',
                    'glass_type.active as glass_type_active',
                    'sub_category.name as sub_category_name',
                    'sub_category.id as sub_category_id',
                    'sub_category.active as sub_category_active',
                    'sub_category.image as image'
                )
                ->join('category', 'category.id', '=', 'products.category_id')
                ->join('sub_category', 'sub_category.id', '=', 'products.sub_category_id')
                ->join('glass_type', 'glass_type.id', '=', 'products.glass_type_id')
                ->first();
            $product->str_created = date('d/m/Y H:i', strtotime($product->created_at));
            $category = Category::find($product->category_id);

        else:
            $product = new Product();
        endif;

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_products'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_products'));

        $this->render('product', [
            'product' => $product,
            'button' => "None",
            'defaultThickness' => $defaultThickness,
            'types' => $types,
            'categories' => $categories,
            'category' => $category,
        ]);
    }

    public function updateProduct($productId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->putStatement();
        $product = Product::find($productId);
        $response = $functionController->baseResponse();

        $product->obs = $data->obs;
        $product->active = (isset($data->active) and $data->active === 'on');
        $product->save();

        $user = User::find($_SESSION['id']);
        $user->setLog('Product', "Usuário atualizou o produto {$product->id}");
        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->url = "/products/";
        $response->dialog = true;
        $response->spinner = true;

        $functionController->sendResponse($response, $status_code);
    }

    public function changeProduct($productId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $response = $functionController->baseResponse();
        $status_code = 200;

        $productModel = new Product();
        $product_search = $productModel->find($productId);

        $product_search->active = !$product_search->active;
        $user = User::find($_SESSION['id']);
        $user->setLog('Product', "O status do produto {$product_search->id} foi modificado para " . ($product_search->active ? "Ativo" : "Inativo") . " - {$_SESSION['name']}");
        $product_search->save();

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->dialog = true;
        $functionController->sendResponse($response, $status_code);
    }
}