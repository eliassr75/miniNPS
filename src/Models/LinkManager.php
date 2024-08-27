<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;
use App\Controllers\FunctionController;

class LinkManager extends Model {
    protected $table = 'link_manager';
    protected $columns = [
        'id',
        'user_id',
        'log_id',
        'action',
        'token',
        'expiration'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;
    public $allowed_keys = [
        'user_id',
        'log_id',
        'action',
        'token',
        'expiration'
    ];

    public function user()
    {
        return User::find($this->user_id);
    }

    public function getAction()
    {
        $functionController = new FunctionController();
        return $functionController->parseJsonToObject($this->action);
    }

    public function setAction($action)
    {
        $functionController = new FunctionController();
        $this->action = $functionController->parseObjectToJson($action);
    }

    public function check_expired(): bool
    {

        $currentDateTime = new \DateTime();
        $expirationDateTime = new \DateTime($this->expiration);

        if ($currentDateTime > $expirationDateTime) {
            return true;
        } else {
            return false;
        }

    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        if(empty($this->token)):
            $this->token = Uuid::uuid4();
        endif;

        return true;
    }

}
?>
