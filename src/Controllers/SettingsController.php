<?php

namespace App\Controllers;

use App\Models\Category;
use App\Models\GlassClearances;
use App\Models\GlassColors;
use App\Models\GlassFinish;
use App\Models\GlassSize;
use App\Models\GlassThickness;
use App\Models\GlassType;
use App\Models\PrintTemplates;
use App\Models\Product;
use App\Models\Registry;
use App\Models\SubCategory;
use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

class SettingsController extends BaseController
{

    public $only_return = false;

    public function addImage()
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();

        if (isset($_FILES['image'])) {

            $uploadDir =  __DIR__ . '/../../public/assets/img/uploads/';
            $file = $_FILES['image'];
            $fileName = $file['name'];
            $fileTmpName = $file['tmp_name'];
            $fileSize = $file['size'];
            $fileError = $file['error'];
            $fileType = $file['type'];

            // Checando erros
            if ($fileError === 0) {
                // Gerando um novo nome de arquivo com hash
                $fileExt = pathinfo($fileName, PATHINFO_EXTENSION);
                $newFileName = hash('sha256', $fileName . time()) . '.' . $fileExt;
                $fileDestination = $uploadDir . $newFileName;

                // Movendo o arquivo para o destino
                if (move_uploaded_file($fileTmpName, $fileDestination)) {
                    $response->image = '/assets/img/uploads/' . $newFileName;
                } else {
                    $response->status = 'error';
                    $response->message = 'Failed to move uploaded file';
                }
            } else {
                $response->status = 'error';
                $response->message = 'File upload error';
            }
        }else{
            $response->image = "/assets/img/sample/photo/1.jpg";
            $response->status = 'warning';
            $response->message = 'Not file founded';
        }

        $functionController->sendResponse($response, $status_code);
    }

    public function print($routeName, $Id, $printId=false)
    {
        $functionController = new FunctionController();

        $urlQrCode = "";
        switch ($routeName) {
            case 'order':
                $urlQrCode = "/order/{$Id}/";
                break;
            case 'products':
                $urlQrCode = "/order/{$Id}/";
            default:
                break;
        }

        $print = PrintTemplates::find($printId);
        $this->render(
            "print_preview", [
                "print" => $print,
                "urlQrCode" => $urlQrCode,
                "routeName" => $routeName,
            ]
        );


    }
    public function update($routeName, $Id)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();
        $response->dialog = true;
        $data = $functionController->putStatement();
        $error = false;


        switch ($routeName) {
            case 'subcategory':

                $response->dialog = false;
                $subCategory = SubCategory::find($Id);

                $subCategory->name = $data->name;
                $subCategory->additional_name = $data->additional_name;
                $subCategory->image = $data->image;
                $subCategory->category_id = $data->category_id;
                $subCategory->glass_type_id = $data->type;
                $subCategory->active = (isset($data->active) and $data->active === 'on');

                if($subCategory->products()->exists()){
                    $subCategory->products()->update([
                        "active" => $subCategory->active,
                    ]);
                }

                $subCategory->save();

                break;
            case 'category':

                $category = Category::find($Id);

                $category->name = $data->name;
                $category->active = (isset($data->active) and $data->active === 'on');

                if($category->products()->exists()){
                    $category->products()->update([
                        "active" => $category->active,
                    ]);
                }

                if($category->sub_categories()->exists()){
                    $category->sub_categories()->update([
                        "active" => $category->active,
                    ]);
                }

                foreach ($category->thickness()->get() as $thick) {
                    $key = "input-{$thick->name}{$thick->type}";
                    if (isset($data->$key)) {
                        $thick->update(['price' => $data->$key]);
                    }
                }

                $category->save();
                break;
            case 'glass_type':
                $response->dialog = false;
                $glass_type = GlassType::find($Id);

                $glass_type->name = $data->name;
                $glass_type->active = (isset($data->active) and $data->active === 'on');

                $glass_type->save();
                break;
            case 'glass_thickness':

                $response->dialog = false;
                $thickness = GlassThickness::find($Id);
                $old_thickness = GlassThickness::find($Id);

                $needsUpdate = false;
                if($thickness->name != $data->name){
                    $needsUpdate = true;
                }
                $thickness->name = $data->name;

                if($thickness->price != $data->price){
                    $needsUpdate = true;
                }
                $thickness->price = $data->price;

                if($thickness->active != (isset($data->active) and $data->active === 'on')){
                    $needsUpdate = true;
                }
                $thickness->active = (isset($data->active) and $data->active === 'on');

                $array = explode("/", $data->type);
                if($thickness->type != end($array)){
                    $needsUpdate = true;
                }
                $thickness->type = end($array);

                if($thickness->category != $array[0]){
                    $needsUpdate = true;
                }
                $thickness->category = $array[0];


                try {
                    if ($needsUpdate) {
                        foreach (Category::all() as $category) {
                            $thick = $category->thickness()
                                ->where('name', $old_thickness->name)
                                ->where('price', $old_thickness->price)
                                ->where('type', $old_thickness->type)
                                ->where('category', $old_thickness->category)
                                ->where('active', $old_thickness->active)
                                ->first();

                            if (!$thick) {
                                $category->thickness()->create([
                                    'name' => $thickness->name,
                                    'price' => $thickness->price,
                                    'type' => $thickness->type,
                                    'category' => $thickness->category,
                                    'active' => $thickness->active,
                                ]);
                            } else {
                                $thick->update([
                                    'name' => $thickness->name,
                                    'price' => $thickness->price,
                                    'type' => $thickness->type,
                                    'category' => $thickness->category,
                                    'active' => $thickness->active,
                                ]);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $error = $e->getMessage();
                }

                $thickness->save();
                break;
            case 'glass_colors':
                $response->dialog = false;
                $glass_color = GlassColors::find($Id);

                $glass_color->name = $data->name;
                $glass_color->percent = $data->percent;
                $glass_color->active = (isset($data->active) and $data->active === 'on');

                $glass_color->save();
                break;
            case 'glass_finish':
                $response->dialog = false;
                $glass_finish = GlassFinish::find($Id);

                $glass_finish->name = $data->name;
                $glass_finish->active = (isset($data->active) and $data->active === 'on');

                $glass_finish->save();
                break;
            case 'glass_clearances':
                $response->dialog = false;
                $glass_clearances = GlassClearances::find($Id);

                $glass_clearances->name = $data->name;
                $glass_clearances->width = $data->width;
                $glass_clearances->height = $data->height;
                $glass_clearances->active = (isset($data->active) and $data->active === 'on');

                $glass_clearances->save();
                break;
            case 'emails':

                $registry = new Registry();
                $registry->set($data->key, $data->email);

                break;
            case 'print_templates':

                $response->dialog = false;
                $template = PrintTemplates::find($Id);
                $template->name = $data->name;
                $template->width = $data->width;
                $template->height = $data->height;
                $template->spacing = $data->spacing;
                $template->active = (isset($data->active) and $data->active === 'on');

                $template->save();
                break;
        }

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->reload = true;
        $response->spinner = true;

        $functionController->sendResponse($response, $status_code);
    }

    public function create($routeName)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $status_code = 200;
        $response = $functionController->baseResponse();
        $response->reload = true;
        $response->spinner = true;
        $response->dialog = true;

        $data = $functionController->postStatement($_POST);
        $response->message = $functionController->locale('register_success_created');
        try{
            switch ($routeName) {
                case 'subcategory':

                    SubCategory::createOrUpdate(
                        [
                            'name' => $data->name,
                            'additional_name' => $data->additional_name,
                            'image' => $data->image,
                            'category_id' => $data->category_id,
                        ],
                        [
                            'name' => $data->name,
                            'additional_name' => $data->additional_name,
                            'image' => $data->image,
                            'glass_type_id' => $data->glass_type_id,
                        ]
                    );
                    $response->dialog = false;

                    break;
                case 'category':
                    $category = new Category();

                    $category->name = $data->name;
                    $category->active = (isset($data->active) and $data->active === 'on');

                    $category->save();
                    $response->url = "/settings/category/{$category->id}/";
                    $response->reload = false;
                    break;
                case 'glass_type':
                    $glass_type = new GlassType();

                    $glass_type->name = $data->name;
                    $glass_type->active = (isset($data->active) and $data->active === 'on');

                    $glass_type->save();
                    $response->dialog = false;
                    break;
                case 'glass_thickness':
                    $thickness = new GlassThickness();

                    $needsCreate = false;
                    if($thickness->name != $data->name) {
                        $needsCreate = true;
                    }

                    $thickness->name = $data->name;
                    $thickness->price = $data->price;
                    $thickness->active = (isset($data->active) and $data->active === 'on');

                    $array = explode("/", $data->type);
                    $thickness->type = end($array);
                    $thickness->category = $array[0];

                    $thickness->save();

                    if($needsCreate){
                        foreach (Category::all() as $category):
                            $category->thickness()->create([
                                'name' => $thickness->name,
                                'price' => $thickness->price,
                                'type' => $thickness->type,
                                'category' => $thickness->category,
                                'active' => $thickness->active,
                            ]);
                        endforeach;
                    }

                    $response->dialog = false;
                    break;
                case 'glass_colors':
                    $glass_color = new GlassColors();

                    $glass_color->name = $data->name;
                    $glass_color->percent = $data->percent;
                    $glass_color->active = (isset($data->active) and $data->active === 'on');

                    $glass_color->save();
                    $response->dialog = false;
                    break;
                case 'glass_finish':
                    $glass_finish = new GlassFinish();

                    $glass_finish->name = $data->name;
                    $glass_finish->active = (isset($data->active) and $data->active === 'on');

                    $glass_finish->save();
                    $response->dialog = false;
                    break;
                case 'glass_clearances':

                    $glass_clearances = new GlassClearances();

                    $glass_clearances->name = $data->name;
                    $glass_clearances->width = $data->width;
                    $glass_clearances->height = $data->height;
                    $glass_clearances->active = (isset($data->active) and $data->active === 'on');

                    $glass_clearances->save();
                    $response->dialog = false;
                    break;

                case 'emails':

                    $registry = new Registry();
                    $registry->set($data->key, $data->email);

                    break;
                case 'print_templates':
                    $template = new PrintTemplates();

                    $template->name = $data->name;
                    $template->width = $data->width;
                    $template->height = $data->height;
                    $template->spacing = $data->spacing;
                    $template->active = (isset($data->active) and $data->active === 'on');

                    $template->save();
                    break;
            }
        }catch (Exception $e){
            $response->message = $e->getMessage();
        }
        $response->status = "success";
        $functionController->sendResponse($response, $status_code);
    }

    public function category($categoryId=false, $json=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_category'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_category'));

        if($categoryId):

            $functionController->is_dashboard(false);

            $data = $functionController->getStatement($_GET);
            $status_code = 200;
            $category = Category::find($categoryId);

            if($json):
                $functionController->api = true;
            endif;

            $thickness = GlassThickness::where('glass_type_id', null)
                ->where('products_id', null)
                ->where('category_id', null)
                ->orderBy('id', 'desc')->get();
            $types = GlassType::where('active', true)->get();

            if($json):
                $response = $functionController->baseResponse();

                $values = [];

                try{
                    if(isset($data->type) && intval($data->type)):
                        $categories = $category->sub_categories()->where('glass_type_id', $data->type)->get();
                    else:
                        $categories = $category->sub_categories()->get();
                    endif;
                }catch (Exception $e){
                    $categories = $category->sub_categories()->get();
                }

                $newProducts = [];
                foreach ($categories as $subCategory):
                    $subCategory->glass_type = GlassType::find($subCategory->glass_type_id);
                    $subCategory->created_text = date('d/m/Y H:i', strtotime($subCategory->created_at));

                    if($subCategory->glass_type_id):
                        Product::updateOrCreate(
                            [
                                'category_id' => $subCategory->category_id,
                                'sub_category_id' => $subCategory->id,
                                'glass_type_id' => $subCategory->glass_type_id,
                            ],
                            [
                                'name' => "{$category->name} {$subCategory->name} " . ($subCategory->glass_type->name ? "({$subCategory->glass_type->name })" : ""),
                                'custom_name' => $subCategory->additional_name
                            ]
                        );

                        /* será migrado para categorias
                        $values = [];
                        if (!$product->thickness()->exists()):
                            foreach ($thickness as $thick):
                                $values[] = [
                                    'name' => $thick->name,
                                    'price' => $thick->price,
                                    'type' => $thick->type,
                                    'category' => $thick->category,
                                    'active' => $thick->active,
                                ];
                            endforeach;
                            $product->thickness()->createMany($values);
                        endif;
                        $values = [];
                        */
                    endif;

                    $values[] =  $subCategory;
                endforeach;

                $response->values = [
                    "subCategories" => $values,
                    "thickness" => $thickness,
                    "types" => $types,
                    "newProducts" => $newProducts,
                ];
                $functionController->sendResponse($response, $status_code);

            else:

                $values = [];
                if (!$category->thickness()->exists()):
                    foreach ($thickness as $thick):
                        $values[] = [
                            'name' => $thick->name,
                            'price' => $thick->price,
                            'type' => $thick->type,
                            'category' => $thick->category,
                            'active' => $thick->active,
                        ];
                    endforeach;
                    $category->thickness()->createMany($values);
                endif;
                $thickness = $category->thickness()->orderBy('id', 'desc')->get();


                $this->render(
                    "category", [
                        "button" => "None",
                        "category" => $category,
                        "thickness" => $thickness,
                        "default" => [
                            "types" => $types
                        ]
                    ]
                );

            endif;

        else:

            $categories = Category::all();
            /*
            $thickness = GlassThickness::where('glass_type_id', null)
                ->where('products_id', null)
                ->where('category_id', null)
                ->orderBy('id', 'desc')->get();
            foreach ($categories as $category) {
                $values = [];
                if (!$category->thickness()->exists()):
                    foreach ($thickness as $thick):
                        $values[] = [
                            'name' => $thick->name,
                            'price' => $thick->price,
                            'type' => $thick->type,
                            'category' => $thick->category,
                            'active' => $thick->active,
                        ];
                    endforeach;
                    $category->thickness()->createMany($values);
                endif;
            }
            */

            $this->render(
                "categories", [
                    "button" => "add",
                    "actionForm" => "addCategory",
                    "categories" => $categories,
                ]
            );
        endif;
    }

    public function glass_type()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        $type = GlassType::all();

        if($this->only_return){
            return $type;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_type'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_type'));

        $this->render(
            "glass_type", [
                "button" => "add",
                "actionForm" => "addType",
                "type" => $type
            ]
        );
    }

    public function glass_thickness()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        $thickness = GlassThickness::where('glass_type_id', null)
            ->where('products_id', null)
            ->where('category_id', null)
            ->orderBy('id', 'desc')->get();

        if($this->only_return){
            return $thickness;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_thickness'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_thickness'));

        $this->render(
            "glass_thickness", [
                "button" => "add",
                "actionForm" => "addThickness",
                "thickness" => $thickness
            ]
        );
    }

    public function glass_colors()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        $colors = GlassColors::all();

        if($this->only_return){
            return $colors;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_colors'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_colors'));

        $this->render(
            "glass_colors", [
                "button" => "add",
                "actionForm" => "addColor",
                "colors" => $colors
            ]
        );
    }

    public function glass_finish()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        $finish = GlassFinish::all();

        if($this->only_return){
            return $finish;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_finish'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_finish'));

        $this->render(
            "glass_finish", [
                "button" => "add",
                "actionForm" => "addFinish",
                "finish" => $finish
            ]
        );
    }

    public function glass_clearances()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        $clearances = GlassClearances::all();

        if($this->only_return){
            return $clearances;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_glass_clearances'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_glass_clearances'));

        $this->render(
            "glass_clearances", [
                "button" => "add",
                "actionForm" => "addClearance",
                "clearances" => $clearances
            ]
        );
    }

    public function print_templates()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_print_templates'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_print_templates'));

        $templates = PrintTemplates::all();

        $this->render(
            "print_templates", [
                "button" => "add",
                "actionForm" => "addPrintTemplates",
                "templates" => $templates
            ]
        );
    }

    public function emails()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('emails_page', true);

        $registry = new Registry();

        if($this->only_return){
            return $registry;
        }

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_emails'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_emails'));

        $this->render(
            "emails", [
                "button" => "add",
                "actionForm" => "addEmails"
            ]
        );
    }

    public function index()
    {

        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('settings_page', true);


        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_settings'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_settings'));


        $options = [
            [
                "name" => $functionController->locale('menu_item_category'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/category/',
                "icon" => 'copy-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_type'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_type/',
                "icon" => 'help-circle-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_thickness'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_thickness/',
                "icon" => 'layers-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_colors'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_colors/',
                "icon" => 'color-palette-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_finish'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_finish/',
                "icon" => 'checkmark-circle-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_glass_clearances'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/glass_clearances/',
                "icon" => 'resize-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_print_templates'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/print_templates/',
                "icon" => 'print-outline',
                "badge" => false
            ],
            [
                "name" => $functionController->locale('menu_item_emails'),
                "permissions" => [1],
                "individual" => false,
                "url" => '/settings/emails/',
                "icon" => 'mail-outline',
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


        $this->render(
        "settings", [
                "button" => "None",
                "menu_options" => $functionController->parseJsonToObject($obj_menu_options)
            ]
        );
    }
}