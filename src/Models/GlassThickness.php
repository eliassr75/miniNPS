<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class GlassThickness extends Model {
    protected $table = 'glass_thickness';
    protected $columns = [
        'id',
        'name',
        'type',
        'category',
        'price',
        'active',
        'products_id',
        'category_id',
        'glass_type_id',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function products(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'products_id', 'id');
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
