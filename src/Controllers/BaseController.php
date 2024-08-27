<?php

namespace App\Controllers;
use Jenssegers\Agent\Agent;

class BaseController
{

    protected $agent;
    protected $headers;

    public function __construct()
    {
        $this->agent = new Agent();
        $this->headers = $this->getHttpHeaders();
    }

    protected function getHttpHeaders(): array
    {
        $headers = [];

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                // Converte o formato de cabeçalho HTTP_ para o formato normal de cabeçalhos
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($key, 5)))));
                $headers[$header] = $value;
            }
        }

        return $headers;
    }

    protected function render($view, $data = [])
    {

        if(!in_array('login', $data)) {
            $data['login'] = false;
        }

        extract($data);
        require "../src/Views/{$view}/index.php";
    }
}
