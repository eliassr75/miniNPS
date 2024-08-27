<?php

namespace App\Models;

use App\Controllers\FunctionController;
use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Controllers\CookieController;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Ramsey\Uuid\Uuid;

class Orders extends Model {
    protected $table = 'orders';
    protected $columns = [
        'id',
        'name',
        'status',
        'url',
        'client_id',
        'user_id',
        'total_price',
        'obs_client',
        'date_delivery',
        'updated_at',
        'created_at'
    ];

    public function status()
    {

    }

    public function log_entry()
    {
        return $this->belongsToMany(LogEntry::class, 'log_entry_user', 'order_id', 'log_id');
    }

    public function setLog($title, $description, $old_dump_post, $new_dump_post)
    {

        $functionController = new FunctionController();
        $log = new LogEntry();
        $log->request_type = $_SERVER['REQUEST_METHOD'];
        $log->user_agent = $_SERVER['HTTP_USER_AGENT'];
        $log->title = $title;
        $log->description = $description;
        $log->old_dump_post = $functionController->parseObjectToJson($old_dump_post);
        $log->new_dump_post = $functionController->parseObjectToJson($new_dump_post);

        if($log->validate()){
            $log->save();
            $this->log_entry()->attach($log->id);
            return $log->id;
        }

    }

    public function items()
    {
        return $this->hasMany(OrdersItems::class, 'order_id', 'id');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        return true;
    }

}

class OrdersItems extends Model
{
    protected $table = 'order_items';
    protected $columns = [
        'id',
        'name',
        'active',
        'order_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'glass_thickness_id',
        'glass_color_id',
        'glass_finish_id',
        'glass_clearances_id',
        'quantity',
        'width',
        'height',
        'obs_factory',
        'obs_client',
        'obs_tempera',
        'price',
        'updated_at',
        'created_at'
    ];

    use HasFactory;
    protected $fillable = [
        'name',
        'active',
        'order_id',
        'category_id',
        'sub_category_id',
        'product_id',
        'glass_thickness_id',
        'glass_color_id',
        'glass_finish_id',
        'glass_clearances_id',
        'glass_type_id',
        'quantity',
        'width',
        'height',
        'obs_factory',
        'obs_client',
        'obs_tempera',
        'price',
        'date_delivery'
    ];

    public function orders(): BelongsTo
    {
        return $this->belongsTo(Orders::class, 'order_id', 'id');
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
