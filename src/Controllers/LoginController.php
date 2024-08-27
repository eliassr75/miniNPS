<?php

namespace App\Controllers;
use App\Models\LinkManager;
use App\Models\PasswordReset;
use App\Models\User;
use App\Validators\Validator;
use Cassandra\Exception\ValidationException;
use Exception;

class LoginController extends BaseController
{

    public function linkManager($token)
    {
        $linkManager = new LinkManager();
        $linkManager = $linkManager->where('token', $token)->first();

        if (!$linkManager || $linkManager->check_expired()) {
            header('Location: /error/404/');
        }else{
            header("Location: /forget-password/{$token}/");
        }

    }

    public function redirectToLogin()
    {
        if (!isset($_SESSION['authenticated'])):
            header('Location: /login/');
        else:
            header('Location: /dashboard/');
        endif;
    }

    public function logout()
    {
        if (isset($_SESSION['id'])):

            $user = User::find($_SESSION['id']);
            $user->auth_hash = null;
            $user->save();
            $user->setLog('Login', 'Sessão encerrada pelo própio usuário.');

        endif;

        session_destroy();
        setcookie('auth_hash', '', time() - 3600, "/", "", false, true);
        $this->redirectToLogin();
    }

    public function authenticate()
    {

        $functions = new FunctionController();
        $functions->api = true;
        $data = $functions->postStatement($_POST);

        $message = ['message' => $functions->locale('invalid_login'), 'status' => 'error'];

        if(!empty($data->username) && !empty($data->password)){

            if(!Validator::validateEmail($data->username)){
                $functions->sendResponse($message, 403);
            }

            if(!isset($_SESSION['access_attempt_counter'])){
                $_SESSION['access_attempt_counter'] = 0;
            }

            $userModel = new User();
            $user_search = $userModel->where('email', str($data->username))->first();
            if(!empty($user_search)){

                $_SESSION['user_language'] = $user_search->language;

                if(!$user_search->permissions()->exists()){

                    $message['message'] = $functions->locale('login_not_permission');
                    $message['status'] = 'warning';
                    $message['dialog'] = true;

                    $user_search->setLog('Login', $functions->locale('try_login_not_permission'));
                    $functions->sendResponse($message, 403);

                }elseif(!$user_search->active){

                    $message['message'] = $functions->locale('login_not_permission');
                    $message['status'] = 'warning';
                    $message['dialog'] = true;

                    $user_search->setLog('Login', "Tentativa de login sem permissão de acesso (Locked).");
                    $functions->sendResponse($message, 403);

                }elseif(!password_verify($data->password, $user_search->password)){

                    $_SESSION['access_attempt_counter']++;
                    $message['message'] = $message['message'] . " <hr class='border'> {$functions->locale('access_attempt_counter')}: " . (4 - $_SESSION['access_attempt_counter']);


                    if((4 - $_SESSION['access_attempt_counter']) === 0){
                        $user_search->active = false;
                        $user_search->save();
                        $user_search->setLog("Login", "Conta bloqueada após 3 tentativas de acesso inválidas.");
                        $_SESSION['access_attempt_counter'] = 0;

                        $message['dialog'] = true;
                        $message['message'] = $functions->locale('locked_account');
                    }

                    $functions->sendResponse($message, 403);

                }else{

                    $user_search->startSession();
                    $missingDataController = new MissingDataController();
                    if($missingDataController->verify($user_search->id)){

                        if(isset($_SESSION["redirect"])){
                            $message['url'] = $_SESSION["redirect"];
                            unset($_SESSION["redirect"]);
                        }else{
                            $message['url'] = '/dashboard/';
                        }
                    }else{
                        $message['url'] = "/missing-data/{$user_search->id}/";
                    }

                    $message['message'] = $functions->locale('welcome') . " - {$user_search->name}";
                    $message['status'] = 'success';
                    $message['custom_timer'] = 2;
                    $message['spinner'] = true;

                    $functions->sendResponse($message);
                }
            }else{
                $functions->sendResponse($message, 404);
            }

        }else{
            $functions->sendResponse($message, 204);
        }
    }

    public function createAccount()
    {
        $functions = new FunctionController();
        $functions->api = true;
        $data = $functions->postStatement($_POST);

        $status_code = 403;
        $message = ['message' => $functions->locale('invalid_login'), 'status' => 'error'];

        try{

            $userModel = new User();
            $userModel->name = $data->name;
            $userModel->email = $data->username;
            $userModel->username = explode('@', $data->username)[0];

            $suggestPassword = "";
            if(isset($data->suggestPassword)):
                $userModel->password = password_hash($functions->defaultPassword(), PASSWORD_BCRYPT);
            else:
                $userModel->password = password_hash($data->password, PASSWORD_BCRYPT);
            endif;


            if($userModel->validate()){

                $byAdmin = "";
                if(isset($data->generate_link)):
                    $userModel->language = $data->language;
                    $userModel->save();
                    $userModel->permissions()->attach($data->permission);
                    $byAdmin = " por administrador ({$_SESSION['name']})";
                else:
                    $userModel->save();
                endif;

                $userModel->setLog("Login", "Conta criada$byAdmin.");

            }

            if(isset($data->generate_link)):

                $bkp_user_language = $_SESSION['user_language'];
                $_SESSION['user_language'] = $userModel->language;

                if(isset($data->suggestPassword)):
                    $suggestPassword =  str_replace('__password__', $functions->defaultPassword(), $functions->locale('email_msg_temp_password'));
                else:
                    $suggestPassword =  str_replace('__password__', $data->password, $functions->locale('email_msg_temp_password'));
                endif;

                $suggestPassword = "<hr> {$functions->locale('input_username')}: {$userModel->email} <br> {$suggestPassword}";
                $token_reset = password_hash("{$userModel->id}-" . date('Y-m-d H:i:s'), PASSWORD_BCRYPT);
                $token_reset = str_replace('/', '', $token_reset);

                $linkManagerModel = new LinkManager();
                $linkManagerModel->user_id = $userModel->id;
                $linkManagerModel->log_id = $userModel->last_log_entry()->id;
                $linkManagerModel->setAction([
                    "action" => "forget-password",
                    "url_for" => "/forget-password/"
                ]);

                $p_resetModel = new PasswordReset();
                $user_reset = $p_resetModel->create([
                    "user_id" => $userModel->id,
                    "token" => $token_reset,
                    "log_id" => $userModel->setLog("Login", "Recuperação de senha pós criação de conta.")
                ]);

                $linkManagerModel->token = $user_reset->token;
                $linkManagerModel->validate();
                $linkManagerModel->save();

                $msg_email = "{$functions->locale('email_msg_user_created')} $suggestPassword";
                $msg_email = str_replace("__name__", $userModel->name, $msg_email);

                $url_reset = $functions->generateCurrentUrl() . "continue/{$user_reset->token}";
                $response_email = $functions->sendMail(
                    $userModel->email, $functions->locale('valid_user_created'),
                    $msg_email, $url_reset,
                    $functions->locale('continue')
                );

                $_SESSION['user_language'] = $bkp_user_language;
                $message['dialog'] = true;
                if(gettype($response_email) == "boolean"):

                    $message['message'] = $functions->locale('valid_user_created');
                    $message['status'] = "info";
                    $message['reload'] = true;
                    $status_code = 200;
                else:

                    $message['message'] = $response_email;
                    $message['status'] = "error";
                    $status_code = 400;
                endif;

            else:

                $message['message'] = $functions->locale('valid_user_created') . " - {$data->name} <hr class='border'>" . $functions->locale('wait_administrator_liberation');
                $message['status'] = 'success';
                $message['url'] = '/login/';
                $message['custom_timer'] = 5;
                $message['spinner'] = true;
                $status_code = 200;

            endif;

        }catch(Exception $e){
            $message['message'] = $e->getMessage();
        }

        $functions->sendResponse($message, $status_code);
    }

    public function resetPassword($token)
    {
        $p_resetModel = new PasswordReset();
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 401;

        $response = $functionController->baseResponse();
        $data = $functionController->putStatement();

        $p_reset_search = $p_resetModel->where('token', $token)->first();
        $user = $p_reset_search ? $p_reset_search->user() : false;

        if (!$p_reset_search || $p_reset_search->check_expired()):

            $response->message = $functionController->locale('expired_link');
            $response->status = "warning";
            $response->custom_timer = 5;
            $response->spinner = true;
            $response->url = "/reset-password/";

        else:

            $user->password = password_hash($data->password, PASSWORD_BCRYPT);
            $user->save();
            $user->setLog("Login", "Usuário redefiniu a própria senha pela página de login.");

            $response->message = $functionController->locale('password_updated');
            $response->status = "success";
            $response->custom_timer = 3;
            $response->spinner = true;
            $response->url = "/login/";

        endif;

        $functionController->sendResponse($response, $status_code);
    }

    public function forgetPassword($token=false)
    {

        $p_resetModel = new PasswordReset();
        $functionController = new FunctionController();
        $functionController->api = true;
        $status_code = 406;
        $method = "POST";
        $data = $functionController->postStatement($_POST);

        if ($token && !empty($data)):
            return;
        else:

            if($token):

                $method = "PUT";
                $p_reset_search = $p_resetModel->where('token', $token)->first();
                $user = $p_reset_search ? $p_reset_search->user() : false;
                $expired = false;
                $response = false;
                $allowed_reset = true;

                if (!$p_reset_search || $p_reset_search->check_expired()):

                    $expired = true;
                    $allowed_reset = false;
                    $method = "POST";
                    $response = $functionController->baseResponse();
                    $response->message = $functionController->locale('expired_link');
                    $response->status = "warning";

                endif;

                define('TITLE_PAGE', 'Fixa Vidros - Recuperação de Senha');
                $this->render('forget_password', [
                    "login" => true,
                    "route" => $expired ? "/forget-password/" : "/forget-password/{$token}/",
                    "expired" => $expired,
                    "response" => $response,
                    "method" => $method,
                    "user" => $user,
                    "allowed_reset" => $allowed_reset
                ]);
                return;
            endif;

            if (!empty($data) and !$token):

                $response = $functionController->baseResponse();
                $response->message = $functionController->locale('invalid_email');
                $response->status = "warning";

                $userModel = new User();
                $user_search = $userModel->where('email', $data->email)->first();
                if(Validator::validateEmail($data->email) && $user_search):

                    $_SESSION['user_language'] = $user_search->language;
                    $token_reset = password_hash("{$user_search->id}-" . date('Y-m-d H:i:s'), PASSWORD_BCRYPT);
                    $token_reset = str_replace('/', '', $token_reset);

                    $user_reset = $p_resetModel->create([
                        "user_id" => $user_search->id,
                        "token" => $token_reset,
                        "log_id" => $user_search->setLog("Login", "Usuário solicitou recuperação de senha")
                    ]);

                    $url_reset = $functionController->generateCurrentUrl() . "{$user_reset->token}";
                    $response_email = $functionController->sendMail(
                        $data->email,
                        $functionController->locale('recovery_password'),
                        "{$functionController->locale('hello')}, {$user_search->name}! <br> {$functionController->locale('recovery_password_email_message')}",
                        $url_reset,
                        $functionController->locale('recovery_password')
                    );

                    if(gettype($response_email) == "boolean"):

                        $response->message = $functionController->locale('sent_email_recovery');
                        $response->status = "info";
                        $status_code = 200;
                    else:
                        $response->message = $response_email;
                        $response->status = "error";
                        $status_code = 400;
                    endif;
                endif;

                $functionController->sendResponse($response, $status_code);
            else:
                define('TITLE_PAGE', 'Fixa Vidros - Recuperação de Senha');
                $this->render('forget_password', [
                    "login" => true,
                    "route" => "/forget-password/",
                    "method" => $method,
                    "expired" => false,
                    "allowed_reset" => false
                ]);
            endif;
        endif;

    }

    public function newAccount()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Nova Conta');
        $this->render('new_account', ["login" => true, "newAccount" => true, "route" => "/new-account/"]);
    }

    public function login()
    {
        define('TITLE_PAGE', 'Fixa Vidros - Login');
        header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");
        $this->render('login', ["login" => true, "route" => "/login/"]);
    }
}