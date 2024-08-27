<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class User extends Model {
    protected $table = 'users';
    protected $columns = [
        'id',
        'active',
        'name',
        'email',
        'username',
        'password',
        'language',
        'token',
        'auth_hash',
        'cpf',
        'rg',
        'phone_number',
        'zip_code',
        'address',
        'complement',
        'address_number',
        'zone',
        'city',
        'state',
        'birthday',
        'age',
        'obs',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];
    public $missingDataKeys = [
        ['name' => 'cpf', 'type' => 'tel', "required" => true, "mask" => "000.000.000-00"],
        ['name' => 'rg', 'type' => 'tel', "required" => false, "mask" => "000000000"],
        ['name' => 'birthday', 'type' => 'tel', "required" => false, "mask" => "00/00/0000"],
        ['name' => 'phone_number', 'type' => 'tel', "required" => true, "mask" => "(00) 00000-0000"],
        ['name' => 'zip_code', 'type' => 'tel', "required" => false, "mask" => "00000-000"],
        ['name' => 'address', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'address_number', 'type' => 'tel', "required" => true, "mask" => "00000000"],
        ['name' => 'zone', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'complement', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'city', 'type' => 'text', "required" => true, "mask" => false],
        ['name' => 'state', 'type' => 'text', "required" => false, "mask" => false],
        ['name' => 'obs', 'type' => 'text', "required" => false, "mask" => false],
    ];

    public function permissions()
    {
        return $this->belongsToMany(Permissions::class, 'permission_user', 'user_id', 'permission_id');
    }

    public function log_entry()
    {
        return $this->belongsToMany(LogEntry::class, 'log_entry_user', 'user_id', 'log_id');
    }

    public function last_log_entry()
    {
        return  $this->log_entry()->orderBy('created', 'desc')->first();
    }

    public function cookie_user_agent(): string|bool
    {
        $last_log = $this->log_entry()->orderBy('created', 'desc')->first();
        return $last_log ? $last_log->user_agent : false;
    }

    public function setLog($title, $description)
    {

        $log = new LogEntry();
        $log->request_type = $_SERVER['REQUEST_METHOD'];
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log->title = $title;
        $log->description = $description;

        if($log->validate()){
            $log->save();
            $this->log_entry()->attach($log->id);
            return $log->id;
        }

    }

    public function current_permission()
    {
        $user = parent::with('permissions')->find($this->id);
        unset($_SESSION['permissions']);
        foreach ($user->permissions as $permission) {
            return $permission->name;
        }
    }

    public function startSession()
    {

        $cookieController = new CookieController();

        foreach ($this->columns as $column) {
            $_SESSION[$column] = $this->$column;
        }

        $_SESSION['active'] = (bool)$this->active;
        $_SESSION['user_language'] = $this->language;
        $_SESSION['authenticated'] = true;

        $user = parent::with('permissions')->find($this->id);
        unset($_SESSION['permissions']);
        foreach ($user->permissions as $permission) {
            $_SESSION['permission_id'] = $permission->id;
            $_SESSION['permission_name'] = $permission->name;
        }
        $cookieController->setAuthCookie($user);
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {

        if(empty($this->token)){
            $this->token = Uuid::uuid4();
        }

        if(empty($this->language)){
            $this->language = "pt";
        }

        return Validator::validateUser($this);
    }

}
?>
