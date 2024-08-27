<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class LogEntry extends Model {
    protected $table = 'log_entry';
    protected $columns = [
        'id',
        'title',
        'description',
        'request_type',
        'old_dump_post',
        'new_dump_post',
        'user_agent'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;
    public $allowed_keys = [
        'title',
        'description',
        'request_type',
        'old_dump_post',
        'new_dump_post',
        'user_agent'
    ];

    public function setLog($title, $description)
    {
        $log = new LogEntry();
        $log->request_type = $_SERVER['REQUEST_METHOD'];
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log->title = $title;
        $log->description = $description;

        if($log->validate()){
            $log->save();
        }
    }


    public function users()
    {
        return $this->belongsToMany(User::class, 'log_entry_user', 'log_id', 'user_id');
    }

    public function products()
    {
        return $this->belongsToMany(Orders::class, 'log_entry_user', 'log_id', 'order_id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return Validator::validateLog($this);
    }

}
?>
