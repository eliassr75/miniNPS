<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Category extends Model {
    protected $table = 'category';
    protected $columns = [
        'id',
        'name',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id', 'id');
    }

    public function sub_categories()
    {
        return $this->hasMany(SubCategory::class, 'category_id', 'id');
    }

    public function thickness()
    {
        return $this->hasMany(GlassThickness::class, 'category_id', 'id');
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
