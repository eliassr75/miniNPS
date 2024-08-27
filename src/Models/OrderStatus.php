<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class OrderStatus extends Model {
    protected $table = 'type_status_orders';
    protected $columns = [
        'id',
        'name',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'status_id', 'id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}

class OrderFinance extends Model {
    protected $table = 'type_status_finance';
    protected $columns = [
        'id',
        'name',
        'active',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'finance_id', 'id');
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
