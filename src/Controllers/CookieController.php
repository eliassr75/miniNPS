<?php

namespace App\Controllers;

use App\Models\LogEntry;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;

class CookieController extends BaseController
{
    public function setAuthCookie($user)
    {
        $hash = password_hash("{$user->id}-" . date('Y-m-d H:i:s'), PASSWORD_BCRYPT);

        setcookie('auth_hash', $hash, time() + (86400 * 30), "/", "", false, true);

        $user->auth_hash = $hash;
        $user->save();
        $user->setLog("Cookies", "Cookie inicializado por 30 dias");
    }

    public function checkAuthCookie()
    {
        if (isset($_COOKIE['auth_hash']) and !isset($_SESSION['authenticated'])):

            $hash = $_COOKIE['auth_hash'];
            if($hash):
                $user = User::where('auth_hash', $hash)->first();
                if($user):
                    $last_user_agent_register = $this->agent;
                    $agent_from_request = $this->agent;

                    $last_user_agent_register->setUserAgent($user->cookie_user_agent());
                    $agent_from_request->setHttpHeaders($this->headers);

                    $device = $last_user_agent_register->device() === $agent_from_request->device();
                    $platform = $last_user_agent_register->platform() === $agent_from_request->platform();
                    $browser = $last_user_agent_register->browser() === $agent_from_request->browser();

                    if ($device and $platform and $browser):
                        $user->startSession();
                        $user->setLog("Login", "Sessão iniciada via cookie.");
                    endif;
                else:
                    $logEntry = new LogEntry();
                    $logEntry->setLog("Cookies", "Tentativa de login via cookie inválida.");
                endif;
            endif;
        endif;
    }
}