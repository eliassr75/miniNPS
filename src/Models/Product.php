<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Product extends Model {
    protected $table = 'products';
    protected $columns = [
        'id',
        'name',
        'active',
        'custom_name',
        'obs',
        'category_id',
        'sub_category_id',
        'glass_type_id',
        'updated_at',
        'created_at'
    ];

    protected $guarded = ['id'];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function sub_category()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_id', 'id');
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
