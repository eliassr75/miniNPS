<?php

namespace App\Controllers;

use GuzzleHttp\Client;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use stdClass;

class FunctionController extends BaseController
{
    public bool $api = false;

    public function defaultPassword(): string
    {
        return "mudar@1234";
    }

    public function replaceHyphensInKeys($array): array
    {
        $newArray = [];

        foreach ($array as $key => $value) {
            $newKey = str_replace('-', '_', $key);
            $newArray[$newKey] = $value;
        }

        return $newArray;
    }

    public function formatCurrencyBR($value) {
        return 'R$ ' . number_format($value, 2, ',', '.');
    }

    public function customPutStatement()
    {
        $input = file_get_contents('php://input');
        // Deserializar os dados
        parse_str($input, $data);

        function arrayToObject($array) {
            if (is_array($array)) {
                $object = new stdClass();
                foreach ($array as $key => $value) {
                    $object->$key = arrayToObject($value);
                }
                return $object;
            } else {
                return $array;
            }
        }

        return arrayToObject($data);
    }

    public function postStatement($data)
    {
        $data = $this->replaceHyphensInKeys($data);
        return json_decode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function getStatement($data)
    {
        $data = $this->replaceHyphensInKeys($data);
        return json_decode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function parseObjectToJson($data): string
    {
        return json_encode($data, true, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }

    public function glassUnits()
    {
        $units = [
            "linear_units" => [
                "title" => [
                    "pt" => "Unidades Lineares",
                    "en" => "Linear Units",
                    "es" => "Unidades Lineales"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "milímetro",
                            "en" => "millimeter",
                            "es" => "milímetro"
                        ],
                        "symbol" => "mm"
                    ],
                    [
                        "name" => [
                            "pt" => "centímetro",
                            "en" => "centimeter",
                            "es" => "centímetro"
                        ],
                        "symbol" => "cm"
                    ],
                    [
                        "name" => [
                            "pt" => "metro",
                            "en" => "meter",
                            "es" => "metro"
                        ],
                        "symbol" => "m"
                    ],
                    [
                        "name" => [
                            "pt" => "polegada",
                            "en" => "inch",
                            "es" => "pulgada"
                        ],
                        "symbol" => "in"
                    ],
                    [
                        "name" => [
                            "pt" => "pé",
                            "en" => "foot",
                            "es" => "pie"
                        ],
                        "symbol" => "ft"
                    ]
                ]
            ],
            "thickness_units" => [
                "title" => [
                    "pt" => "Unidades de Espessura",
                    "en" => "Thickness Units",
                    "es" => "Unidades de Espesor"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "milímetro",
                            "en" => "millimeter",
                            "es" => "milímetro"
                        ],
                        "symbol" => "mm"
                    ]
                ]
            ],
            "area_units" => [
                "title" => [
                    "pt" => "Unidades de Área",
                    "en" => "Area Units",
                    "es" => "Unidades de Área"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "centímetro quadrado",
                            "en" => "square centimeter",
                            "es" => "centímetro cuadrado"
                        ],
                        "symbol" => "cm²"
                    ],
                    [
                        "name" => [
                            "pt" => "metro quadrado",
                            "en" => "square meter",
                            "es" => "metro cuadrado"
                        ],
                        "symbol" => "m²"
                    ],
                    [
                        "name" => [
                            "pt" => "polegada quadrada",
                            "en" => "square inch",
                            "es" => "pulgada cuadrada"
                        ],
                        "symbol" => "in²"
                    ],
                    [
                        "name" => [
                            "pt" => "pé quadrado",
                            "en" => "square foot",
                            "es" => "pie cuadrado"
                        ],
                        "symbol" => "ft²"
                    ]
                ]
            ],
            "volume_units" => [
                "title" => [
                    "pt" => "Unidades de Volume",
                    "en" => "Volume Units",
                    "es" => "Unidades de Volumen"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "litro",
                            "en" => "liter",
                            "es" => "litro"
                        ],
                        "symbol" => "L"
                    ],
                    [
                        "name" => [
                            "pt" => "metro cúbico",
                            "en" => "cubic meter",
                            "es" => "metro cúbico"
                        ],
                        "symbol" => "m³"
                    ],
                    [
                        "name" => [
                            "pt" => "polegada cúbica",
                            "en" => "cubic inch",
                            "es" => "pulgada cúbica"
                        ],
                        "symbol" => "in³"
                    ],
                    [
                        "name" => [
                            "pt" => "pé cúbico",
                            "en" => "cubic foot",
                            "es" => "pie cúbico"
                        ],
                        "symbol" => "ft³"
                    ]
                ]
            ],
            "weight_units" => [
                "title" => [
                    "pt" => "Unidades de Peso",
                    "en" => "Weight Units",
                    "es" => "Unidades de Peso"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "grama",
                            "en" => "gram",
                            "es" => "gramo"
                        ],
                        "symbol" => "g"
                    ],
                    [
                        "name" => [
                            "pt" => "quilograma",
                            "en" => "kilogram",
                            "es" => "kilogramo"
                        ],
                        "symbol" => "kg"
                    ],
                    [
                        "name" => [
                            "pt" => "tonelada",
                            "en" => "ton",
                            "es" => "tonelada"
                        ],
                        "symbol" => "t"
                    ],
                    [
                        "name" => [
                            "pt" => "libra",
                            "en" => "pound",
                            "es" => "libra"
                        ],
                        "symbol" => "lb"
                    ]
                ]
            ],
            "other_units" => [
                "title" => [
                    "pt" => "Outras Unidades",
                    "en" => "Other Units",
                    "es" => "Otras Unidades"
                ],
                "units" => [
                    [
                        "name" => [
                            "pt" => "micrômetro",
                            "en" => "micrometer",
                            "es" => "micrómetro"
                        ],
                        "symbol" => "µm"
                    ]
                ]
            ]
        ];

        return $units;

    }

    public function validaCPF($cpf) {

        // Remove caracteres não numéricos
        $cpf = preg_replace('/[^0-9]/', '', $cpf);

        // Verifica se o CPF possui 11 dígitos
        if (strlen($cpf) != 11) {
            return false;
        }

        // Verifica se todos os dígitos são iguais (caso contrário, não é um CPF válido)
        if (preg_match('/(\d)\1{10}/', $cpf)) {
            return false;
        }

        // Calcula o primeiro dígito verificador
        $sum = 0;
        for ($i = 0; $i < 9; $i++) {
            $sum += $cpf[$i] * (10 - $i);
        }
        $digit1 = 11 - ($sum % 11);
        if ($digit1 >= 10) {
            $digit1 = 0;
        }

        // Calcula o segundo dígito verificador
        $sum = 0;
        for ($i = 0; $i < 10; $i++) {
            $sum += $cpf[$i] * (11 - $i);
        }
        $digit2 = 11 - ($sum % 11);
        if ($digit2 >= 10) {
            $digit2 = 0;
        }

        // Verifica se os dígitos verificadores calculados são iguais aos fornecidos
        if ($cpf[9] == $digit1 && $cpf[10] == $digit2) {
            return true;
        } else {
            return false;
        }
    }

    public function validateCNPJ($cnpj)
        {
            // Remove caracteres não numéricos
        $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

            // Verifica se o CNPJ tem 14 dígitos
        if (strlen($cnpj) != 14) {
        return false;
        }

        // Verifica se todos os dígitos são iguais
        if (preg_match('/(\d)\1{13}/', $cnpj)) {
            return false;
        }

        // Validação do primeiro dígito verificador
        $weights = [5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 12; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $firstCheckDigit = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);
        if ($cnpj[12] != $firstCheckDigit) {
            return false;
        }

        // Validação do segundo dígito verificador
        $weights = [6, 5, 4, 3, 2, 9, 8, 7, 6, 5, 4, 3, 2];
        $sum = 0;
        for ($i = 0; $i < 13; $i++) {
            $sum += $cnpj[$i] * $weights[$i];
        }
        $secondCheckDigit = ($sum % 11) < 2 ? 0 : 11 - ($sum % 11);
        if ($cnpj[13] != $secondCheckDigit) {
            return false;
        }

        return true;
    }


    public function putStatement(): stdClass
    {
        // Ler o fluxo de entrada bruta
        $input = file_get_contents('php://input');

        // Separar os dados no formato multipart/form-data
        $boundary = substr($input, 0, strpos($input, "\r\n"));
        $parts = array_slice(explode($boundary, $input), 1);

        $data = [];

        foreach ($parts as $part) {
            if ($part == "--\r\n") {
                break;
            }

            // Remover cabeçalhos do conteúdo
            $part = ltrim($part, "\r\n");
            list($headers, $body) = explode("\r\n\r\n", $part, 2);

            // Lidar com dados de arquivo
            if (preg_match('/name=\"([^\"]*)\"; filename=\"([^\"]*)\"/', $headers, $matches)) {
                $name = $matches[1];
                $filename = $matches[2];
                file_put_contents($filename, trim($body));
                $data[$name] = $filename;
            } elseif (preg_match('/name=\"([^\"]*)\"/', $headers, $matches)) {
                // Lidar com dados de campo
                $name = $matches[1];
                $data[$name] = trim($body);
            }
        }

        // Agora $data contém os dados do formulário
        return json_decode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function parseJsonToObject($data): array
    {
        return json_decode(json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    }

    public function sendResponse($responseData, $statusCode = 200)
    {
        if($this->api){
            header("Content-type: application/json; charset=utf-8", true, $statusCode);
            echo json_encode($responseData, true, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }else{
            return $responseData;
        }

    }

    public function baseResponse(): stdClass
    {
        $response = new stdClass();

        $response->message = "";
        $response->status = "";
        $response->url = false;
        $response->custom_timer = false;
        $response->reload = false;
        $response->dialog = false;
        $response->spinner = false;

        return $response;
    }

    public function is_dashboard($is)
    {
        $_SESSION['dashboard'] = $is;
    }

    public function is_($label, $is)
    {
        $_SESSION[$label] = $is;
    }

    public function exportVarsToJS($data)
    {
        $jsContent = ""; // Inicializa a variável para armazenar o conteúdo do JavaScript

        foreach ($data as $key => $value) { // Corrige a sintaxe do foreach
            // Concatena o conteúdo do JavaScript
            $jsContent .= "const $key = " . json_encode($value, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ";\n";
        }

        // Caminho do arquivo JavaScript
        $jsFilePath = "../public/assets/js/custom_vars.js";

        // Escrever no arquivo JavaScript
        file_put_contents($jsFilePath, $jsContent);
    }

    public function locale($key): string
    {
        if(!isset($_SESSION['user_language'])){
            $_SESSION['user_language'] = 'pt';
        }

        $content = file_get_contents("../config/locales/locale.json");
        $locales = json_decode($content, true);

        $js_locale = [];
        foreach ($locales as $key_new => $locale) {
            $js_locale[$key_new] = $locale[$_SESSION['user_language']];
        }

        // Gerar o conteúdo do arquivo JavaScript
        $jsContent = "const locale = " . json_encode($js_locale, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) . ";";

        // Caminho do arquivo JavaScript
        $jsFilePath = "../public/assets/js/locale_{$_SESSION['user_language']}.js";

        // Escrever no arquivo JavaScript
        file_put_contents($jsFilePath, $jsContent);

        $value = $locales[$key][$_SESSION['user_language']];
        if ($this->api){
            return $value;
        }
        return $value;
    }

    public function timeDiff($startTime, $endTime, $unit = 'seconds') {
        $start = new \DateTime($startTime);
        $end = new \DateTime($endTime);
        $diff = $start->diff($end);

        switch ($unit) {
            case 'years':
                return $diff->y;
            case 'months':
                return ($diff->y * 12) + $diff->m;
            case 'days':
                return ($diff->days);
            case 'hours':
                return ($diff->days * 24) + $diff->h;
            case 'minutes':
                return (($diff->days * 24 * 60) + ($diff->h * 60) + $diff->i);
            case 'seconds':
            default:
                return ((($diff->days * 24 * 60 * 60) + ($diff->h * 60 * 60) + ($diff->i * 60) + $diff->s));
        }
    }

    public function normalizeString($str): string|null
    {
        $str = strtolower($str);
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
        $str = preg_replace('/[^a-z0-9\-]+/', '-', $str);
        $str = trim($str, '-');
        $str = preg_replace('/\-+/', '-', $str);

        return $str;
    }

    public function generateCurrentUrl($customURL=false) {

        // Determine the scheme (http or https)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';

        // Get the host (domain or IP)
        $host = $_SERVER['HTTP_HOST'];

        // Get the path of the current script
        $path = $_SERVER['REQUEST_URI'];

        // Combine to form the full URL
        if(!$customURL){
            $url = $scheme . '://' . $host . $path;
        }else{
            $url = $scheme . '://' . $host . $customURL;
        }

        return $url;
    }

    public function sendMail($para, $assunto, $msg, $url=false, $btn=false): bool|string
    {
        //Create an instance; passing `true` enables exceptions
        $mail = new PHPMailer(true);

        if($btn){
            $btn = "
                <table class='s-4 w-full' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%;' width='100%'>
                    <tbody>
                        <tr>
                            <td style='line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;' align='left' width='100%' height='16'>
                            &#160;
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class='btn btn-primary p-3 fw-700' role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-radius: 6px; border-collapse: separate !important; font-weight: 700 !important;'>
                    <tbody>
                    <tr>
                        <td style='line-height: 24px; font-size: 16px; border-radius: 6px; font-weight: 700 !important; margin: 0;' align='center' bgcolor='#0d6efd'>
                            <a href='{$url}' style='color: #ffffff; font-size: 16px; font-family: Helvetica, Arial, sans-serif; text-decoration: none; border-radius: 6px; line-height: 20px; display: block; font-weight: 700 !important; white-space: nowrap; background-color: #31D2F2; padding: 12px;'>{$btn}</a>
                        </td>
                    </tr>
                    </tbody>
                </table>
        ";
        }else{
            $btn = "";
        }

        try {
            //Server settings
            //$mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
            $mail->isSMTP();                                              //Send using SMTP
            $mail->Host       = MAIL_HOST;                                //Set the SMTP server to send through
            $mail->SMTPAuth   = true;                                     //Enable SMTP authentication
            $mail->Username   = MAIL_USERNAME;                            //SMTP username
            $mail->Password   = MAIL_PASSWORD;                            //SMTP password
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;           //Enable implicit TLS encryption
            $mail->Port       = MAIL_PORT;                                //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

            $mail->CharSet = 'UTF-8';
            //Recipients
            $mail->setFrom(MAIL_USERNAME);
            //$mail->addAddress('joe@example.net');     //Add a recipient
            $mail->addAddress($para);                   //Name is optional

            //Attachments
            //$mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

            //Content
            $mail->isHTML(true);                              //Set email format to HTML
            $mail->Subject = $assunto;
            $mail->Body    = "
        
        <!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
        <html>
            <head>
                <!-- Compiled with Bootstrap Email version: 1.3.1 -->
                <meta http-equiv='x-ua-compatible' content='ie=edge'>
                <meta name='x-apple-disable-message-reformatting'>
                <meta name='viewport' content='width=device-width, initial-scale=1'>
                <meta name='format-detection' content='telephone=no, date=no, address=no, email=no'>
                <meta http-equiv='Content-Type' content='text/html; charset=utf-8'>
                <style type='text/css'>
                body,table,td{font-family:Helvetica,Arial,sans-serif !important}.ExternalClass{width:100%}.ExternalClass,.ExternalClass p,.ExternalClass span,.ExternalClass font,.ExternalClass td,.ExternalClass div{line-height:150%}a{text-decoration:none}*{color:inherit}a[x-apple-data-detectors],u+#body a,#MessageViewBody a{color:inherit;text-decoration:none;font-size:inherit;font-family:inherit;font-weight:inherit;line-height:inherit}img{-ms-interpolation-mode:bicubic}table:not([class^=s-]){font-family:Helvetica,Arial,sans-serif;mso-table-lspace:0pt;mso-table-rspace:0pt;border-spacing:0px;border-collapse:collapse}table:not([class^=s-]) td{border-spacing:0px;border-collapse:collapse}@media screen and (max-width: 600px){.w-full,.w-full>tbody>tr>td{width:100% !important}.p-lg-10:not(table),.p-lg-10:not(.btn)>tbody>tr>td,.p-lg-10.btn td a{padding:0 !important}.p-3:not(table),.p-3:not(.btn)>tbody>tr>td,.p-3.btn td a{padding:12px !important}.p-6:not(table),.p-6:not(.btn)>tbody>tr>td,.p-6.btn td a{padding:24px !important}*[class*=s-lg-]>tbody>tr>td{font-size:0 !important;line-height:0 !important;height:0 !important}.s-4>tbody>tr>td{font-size:16px !important;line-height:16px !important;height:16px !important}.s-10>tbody>tr>td{font-size:40px !important;line-height:40px !important;height:40px !important}}
                </style>
            </head>
            <body style='outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;' bgcolor='#ffffff'>
                <table class='body' valign='top' role='presentation' border='0' cellpadding='0' cellspacing='0' style='outline: 0; width: 100%; min-width: 100%; height: 100%; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; font-family: Helvetica, Arial, sans-serif; line-height: 24px; font-weight: normal; font-size: 16px; -moz-box-sizing: border-box; -webkit-box-sizing: border-box; box-sizing: border-box; color: #000000; margin: 0; padding: 0; border-width: 0;' bgcolor='#ffffff'>
            <tbody>
              <tr>
                <td valign='top' style='line-height: 24px; font-size: 16px; margin: 0;' align='left'>
                  <table class='container' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%;'>
                    <tbody>
                      <tr>
                        <td align='center' style='line-height: 24px; font-size: 16px; margin: 0; padding: 0 16px;'>
                          <!--[if (gte mso 9)|(IE)]>
                            <table align='center' role='presentation'>
                              <tbody>
                                <tr>
                                  <td width='600'>
                          <![endif]-->
                          <table align='center' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%; max-width: 600px; margin: 0 auto;'>
                            <tbody>
                              <tr>
                                <td style='line-height: 24px; font-size: 16px; margin: 0;' align='left'>
                                  <table class='ax-center' role='presentation' align='center' border='0' cellpadding='0' cellspacing='0' style='margin: 0 auto;'>
                                    <tbody>
                                      <tr>
                                        <td style='line-height: 24px; font-size: 16px; margin: 0;' align='left'>
                                          <div class='text-center' style='' align='center'>
                                            <table class='s-10 w-full' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%;' width='100%'>
                                              <tbody>
                                                <tr>
                                                  <td style='line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;' align='left' width='100%' height='40'>
                                                    &#160;
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                            <img class='' width='50%' src='https://fixavidros.etecsystems.com.br/assets/img/logo-fixa-new.png' style='height: auto; line-height: 100%; outline: none; text-decoration: none; display: block; border-style: none; border-width: 0;'>
                                            <table class='s-10 w-full' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%;' width='100%'>
                                              <tbody>
                                                <tr>
                                                  <td style='line-height: 40px; font-size: 40px; width: 100%; height: 40px; margin: 0;' align='left' width='100%' height='40'>
                                                    &#160;
                                                  </td>
                                                </tr>
                                              </tbody>
                                            </table>
                                          </div>
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                  <table class='card p-6 p-lg-10 space-y-4' role='presentation' border='0' cellpadding='0' cellspacing='0' style='border-radius: 6px; border-collapse: separate !important; width: 100%; overflow: hidden; border: 1px solid #e2e8f0;' bgcolor='#ffffff'>
                                    <tbody>
                                      <tr>
                                        <td style='line-height: 24px; font-size: 16px; width: 100%; margin: 0; padding: 40px;' align='left' bgcolor='#ffffff'>
                                          <h1 class='h3 fw-700' style='padding-top: 0; padding-bottom: 0; font-weight: 700 !important; vertical-align: baseline; font-size: 28px; line-height: 33.6px; margin: 0;' align='left'>{$assunto}</h1>
                                          <table class='s-4 w-full' role='presentation' border='0' cellpadding='0' cellspacing='0' style='width: 100%;' width='100%'>
                                            <tbody>
                                              <tr>
                                                <td style='line-height: 16px; font-size: 16px; width: 100%; height: 16px; margin: 0;' align='left' width='100%' height='16'>
                                                  &#160;
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                          <div class='' style='line-height: 24px; font-size: 16px; width: 100%; margin: 0;' align='left'>{$msg}</div>
                                          {$btn}
                                          
                                        </td>
                                      </tr>
                                    </tbody>
                                  </table>
                                </td>
                              </tr>
                            </tbody>
                          </table>
                          <!--[if (gte mso 9)|(IE)]>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                          <![endif]-->
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
            </tbody>
          </table>
            </body>
      </html>
        
            ";

            $mail->send();
            return true;
        } catch (Exception $e) {
            return $mail->ErrorInfo;
        }
    }

    public function sendRequest($requestData, $url, $method)
    {
        $client = new Client();

        $response = $client->$method($url, [
            'headers' => ['Content-Type' => 'application/json'],
            ($method == "get" ? 'query': 'body') => ($method == "get" ? $requestData : json_encode($requestData, true))
        ]);

        try {
            return json_decode($response->getBody());
        }catch (Exception $e){
            return ["error" => $e->getMessage()];
        }
    }

}