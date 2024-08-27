<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class GlassColors extends Model {
    protected $table = 'glass_colors';
    protected $columns = [
        'id',
        'name',
        'percent',
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
