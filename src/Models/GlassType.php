<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class GlassType extends Model {
    protected $table = 'glass_type';
    protected $columns = [
        'id',
        'name',
        'ncm',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}
?>
