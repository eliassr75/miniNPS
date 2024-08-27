<?php

namespace App\Models;

use Exception;
use App\Validators\Validator;
use Illuminate\Database\Eloquent\Model;
use App\Controllers\CookieController;
use Ramsey\Uuid\Uuid;

class Registry extends Model {
    protected $table = 'registry';
    protected $columns = [
        'id',
        'key',
        'value',
        'created_at',
        'updated_at'
    ];

    protected $guarded = ['id'];

    protected $allowed_keys = [
        'email_production',
        'email_finance',
        'email_fabric',
    ];

    public function set($key, $value)
    {
        if (!in_array($key, $this->allowed_keys)) {
            throw new Exception('Invalid key: ' . $key);
        }else{
            $this->updateOrCreate(
                ['key' => $key],
                ['value' => $value]
            );
        }
    }

    public function unset($key)
    {
        if($this->where('key', $key)->exists()) {
            $this->where('key', $key)->delete();
        }
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
