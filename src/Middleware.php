<?php

namespace App;
use App\Controllers\CookieController;

class Middleware
{
    public function is_authenticated(): bool
    {
        $loginCookie = new CookieController();
        $loginCookie->checkAuthCookie();

        if (isset($_SESSION['authenticated']) && $_SESSION['authenticated']):
            return true;
        else:
            return false;
        endif;
    }

    public function autoRedirect()
    {
        $loginCookie = new CookieController();
        $loginCookie->checkAuthCookie();

        if (!isset($_SESSION['authenticated']) and !isset($_SESSION['user_language'])):
            header('Location: /');
        endif;
    }

    /*TODO: terminar esta função depois*/
    public function detectErrors()
    {
        set_exception_handler(function($exception) {
            http_response_code(500);
            header('/error/500/');
        });
    }
}
