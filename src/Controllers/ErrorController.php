<?php

namespace App\Controllers;

use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

define('TITLE_PAGE', 'Erro');

class ErrorController extends BaseController
{
    public function errorPage($codeError)
    {

        try{
            $codeError = intval($codeError);
        }catch (Exception $e){
            $codeError = 404;
        }

        if ($codeError == 0 or $codeError < 400) {
            $codeError = 404;
        }

        $this->render(
        "error", [
                "login" => true,
                "code" => $codeError
            ]
        );
    }
}