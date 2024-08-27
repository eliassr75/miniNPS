<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class SubCategory extends Model {
    protected $table = 'sub_category';
    protected $columns = [
        'id',
        'name',
        'additional_name',
        'active',
        'image',
        'category_id',
        'glass_type_id',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function categories()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_id', 'id');
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
