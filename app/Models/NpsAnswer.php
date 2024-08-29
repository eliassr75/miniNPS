<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class NpsAnswer extends Pivot
{
    use HasFactory;

    protected $table = 'nps_answers';

    protected $fillable = [
        'nps_id',
        'user_id',
        'value',
        'answer',
    ];
}
