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
use App\Models\SubCategory;
use App\Models\User;
use Exception;
use Statickidz\GoogleTranslate;

class UploadsController extends BaseController
{

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
}