<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Entity extends Model
{
    use HasFactory;

    protected $table = 'entity';

    protected $fillable = [
        'name',
        'active',
        'cnpj',
        'token'
    ];

    public function users()
    {
        return $this->hasMany(User::class, 'user_id', 'id');
    }

    public function nps()
    {
        return $this->hasMany(Nps::class, 'user_id', 'id');
    }
}
