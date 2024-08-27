<?php
// Configuração CORS

$maxSessionTime = 3600*5;
// Define o tempo de vida dos cookies da sessão
session_set_cookie_params($maxSessionTime);
// Define o tempo de vida da sessão
ini_set('session.gc_maxlifetime', $maxSessionTime);

session_start();
header("Access-Control-Allow-Origin: *");
header('Access-Control-Allow-Methods: *');
header("Access-Control-Allow-Headers: *");
date_default_timezone_set("America/Sao_Paulo");
