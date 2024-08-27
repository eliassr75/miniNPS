<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

class Permissions extends Model {
    protected $table = 'permissions';
    protected $columns = [
        'id',
        'name',
        'description',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];
    public $timestamps = false;
    public $allowed_keys = [
        'name',
        'description',
    ];


    public function users()
    {
        return $this->belongsToMany(User::class, 'permission_user', 'permission_id', 'user_id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}
?>
