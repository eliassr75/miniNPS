<?php

namespace App\Controllers;

use App\Models\Client;
use App\Models\Permissions;
use App\Models\User;
use Doctrine\Inflector\Rules\Transformation;
use Exception;

class ClientController extends BaseController
{
    public function index()
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(true);
        $functionController->is_('clients_page', true);

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_client'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_client'));

        $clients = Client::orderBy('id', 'desc')->get();
        $clients_array = [];
        foreach ($clients as $client) {
            $client->str_created = date('d/m/Y H:i', strtotime($client->created_at));
            $clients_array[] = $client;
        }

        $this->render('clients', [
            'clients' => $clients_array,
            'button' => 'add',
            'url' => '/client/new/'
        ]);
    }

    public function json($clientId=false)
    {
        $functionController = new FunctionController();
        $functionController->api = true;

        $user = false;
        $users_array = [];

        if($clientId):
            $user = Client::find($clientId);
            $user->str_created = date('d/m/Y H:i', strtotime($user->created_at));
            $user->current_permission = $user->current_permission();

            $newMissingDataKeys = [];
            foreach ($user->missingDataKeys as $missingDataKey) {
                $key_name = $missingDataKey['name'];
                $missingDataKey['label'] = ucfirst($functionController->locale("input_{$missingDataKey['name']}"));
                if ($key_name === "birthday"):
                    $missingDataKey['value'] = $user->$key_name ? date('d/m/Y', strtotime($user->$key_name)) : null;
                else:
                    $missingDataKey['value'] = $user->$key_name;
                endif;
                $newMissingDataKeys[] = $missingDataKey;
            }
            $user->missing_data = $newMissingDataKeys;

        else:
            $users = Client::with('permissions')->orderBy('id', 'desc')->get();
            foreach ($users as $user_obj) {
                $user_obj->str_created = date('d/m/Y H:i', strtotime($user_obj->created_at));
                $user_obj->str_updated = date('d/m/Y H:i', strtotime($user_obj->updated_at));
                $user_obj->str_birthday = date('d/m/Y', strtotime($user_obj->birthday));
                $user_obj->current_permission = $user_obj->current_permission();

                $newMissingDataKeys = [];
                foreach ($user_obj->missingDataKeys as $missingDataKey) {
                    $key_name = $missingDataKey['name'];
                    $missingDataKey['label'] = $functionController->locale("input_{$missingDataKey['name']}");
                    $missingDataKey['value'] = $user_obj->$key_name;
                    $newMissingDataKeys[] = $missingDataKey;
                }
                $user_obj->missing_data = $newMissingDataKeys;

                $users_array[] = $user_obj;
            }
        endif;

        $permissions = Permissions::orderBy('id', 'asc')->get();
        $functionController->sendResponse([
            'users' => $users_array,
            'user' => $user,
            'permissions' => $permissions,
        ]);
    }

    public function getClient($clientId=false)
    {
        $functionController = new FunctionController();
        $functionController->is_dashboard(false);
        $functionController->is_('clients_page', true);

        if($clientId):
            $client = Client::find($clientId);
            $client->str_created = date('d/m/Y H:i', strtotime($client->created_at));
        else:
            $client = new Client();
        endif;

        $newMissingDataKeys = [];
        foreach ($client->missingDataKeys as $missingDataKey) {
            $key_name = $missingDataKey['name'];
            $missingDataKey['label'] = ucfirst($functionController->locale("input_{$missingDataKey['name']}"));
            if ($key_name === "birthday"):
                $missingDataKey['value'] = $client->$key_name ? date('d/m/Y', strtotime($client->$key_name)) : null;
            else:
                $missingDataKey['value'] = $client->$key_name;
            endif;
            $newMissingDataKeys[] = $missingDataKey;
        }
        $client->missing_data = $newMissingDataKeys;

        define('TITLE_PAGE', 'Fixa Vidros - ' . $functionController->locale('menu_item_client'));
        define('SUBTITLE_PAGE', $functionController->locale('menu_item_client'));

        $this->render('client', [
            'client' => $client,
            'button' => "None"
        ]);
    }

    public function newClient()
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->postStatement($_POST);
        $client = new Client();

        $response = $functionController->baseResponse();
        $invalid_document = false;

        foreach ($client->missingDataKeys as $missingDataKey) {
            $key_name = $missingDataKey['name'];
            
            if ($key_name === 'birthday'):
                if (!empty($data->$key_name)):
                    $birthday = str_replace('/', '-', $data->$key_name);
                    $birthday = date('Y-m-d', strtotime($birthday));

                    $client->$key_name = $birthday;
                    $client->age = $functionController->timeDiff($birthday, date('Y-m-d'), 'years');
                endif;
            elseif($key_name === 'document'):

                $cpf = $functionController->validaCPF($data->document);
                $cnpj = $functionController->validateCNPJ($data->document);

                if(!$cpf and !$cnpj):
                    $invalid_document = true;
                    break;
                else:
                    $client->$key_name = $data->$key_name;
                endif;
            elseif ($key_name === 'phone_number'):
                if (strlen($data->$key_name) >= 14):
                    $client->$key_name = $data->$key_name;
                endif;
            else:
                $client->$key_name = $data->$key_name;
            endif;
        }

        if ($invalid_document):

            $response->message = $functionController->locale('invalid_document');
            $response->dialog = true;
            $response->status = "warning";
            $status_code = 400;
        else:

            $client->validate();
            $client->save();
            $user = User::find($_SESSION['id']);
            $user->setLog('Client', "Usuário criou o cliente {$client->id}");
            $response->message = $functionController->locale('register_success_update');
            $response->status = "success";
            $response->url = "/clients/";
            $response->dialog = true;
            $response->spinner = true;
        endif;

        $functionController->sendResponse($response, $status_code);
    }

    public function updateClient($clientId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 200;

        $data = $functionController->putStatement();
        $client = Client::find($clientId);
        $response = $functionController->baseResponse();
        $invalid_document = false;

        foreach ($client->missingDataKeys as $missingDataKey) {
            $key_name = $missingDataKey['name'];

            if ($key_name === 'birthday'):
                if (!empty($data->$key_name)):
                    $birthday = str_replace('/', '-', $data->$key_name);
                    $birthday = date('Y-m-d', strtotime($birthday));

                    $client->$key_name = $birthday;
                    $client->age = $functionController->timeDiff($birthday, date('Y-m-d'), 'years');
                endif;
            elseif($key_name === 'document'):

                $cpf = $functionController->validaCPF($data->document);
                $cnpj = $functionController->validateCNPJ($data->document);

                if(!$cpf and !$cnpj):
                    $invalid_document = true;
                    break;
                else:
                    $client->$key_name = $data->$key_name;
                endif;
            elseif ($key_name === 'phone_number'):
                if (strlen($data->$key_name) >= 14):
                    $client->$key_name = $data->$key_name;
                endif;
            else:
                $client->$key_name = $data->$key_name;
            endif;
        }

        if ($invalid_document):

            $response->message = $functionController->locale('invalid_document');
            $response->dialog = true;
            $response->status = "warning";
            $status_code = 400;
        else:

            $client->validate();
            $client->save();
            $user = User::find($_SESSION['id']);
            $user->setLog('Client', "Usuário atualizou o cliente {$client->id}");
            $response->message = $functionController->locale('register_success_update');
            $response->status = "success";
            $response->url = "/clients/";
            $response->dialog = true;
            $response->spinner = true;
        endif;

        $functionController->sendResponse($response, $status_code);
    }

    public function changeClient($clientId)
    {
        $functionController = new FunctionController();
        $functionController->api = true;
        $response = $functionController->baseResponse();
        $status_code = 200;

        $clientModel = new Client();
        $client_search = $clientModel->find($clientId);

        $client_search->active = !$client_search->active;
        $user = User::find($_SESSION['id']);
        $user->setLog('Client', "O status do client {$user->id} foi modificado para " . ($client_search->active ? "Ativo" : "Inativo") . " - {$_SESSION['name']}");
        $client_search->save();

        $response->message = $functionController->locale('register_success_update');
        $response->status = "success";
        $response->dialog = true;
        $functionController->sendResponse($response, $status_code);
    }
}