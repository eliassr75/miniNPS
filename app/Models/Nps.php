<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Maize\Nps\DefaultNpsRange;
use Maize\Nps\DefaultNpsVisibility;
use Maize\Nps\EmojiNpsRange;
use Maize\Nps\MinimalNpsRange;

class Nps extends Model
{
    use HasFactory;

    protected $table = 'nps';

    protected $fillable = [
        'question',
        'starts_at',
        'ends_at',
        'range',
        'visibility',
        'entity_id'
    ];

    protected $casts = [
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
    ];

    public function answers()
    {
        return $this->hasMany(NpsAnswer::class, 'nps_id', 'id');
    }

    public function entity()
    {
        return $this->belongsTo(Entity::class, 'entity_id', 'id');
    }

    public static function npsCacheKey(): string
    {
        return "nps.current";
    }

    public function isVisible(): bool
    {
        $visibility = false;
        if($this->visibility === "default"){
            $visibility = true;
        }

        return $visibility;
    }

    public function getValuesAttribute(): array
    {
        $range = [
            'default' => DefaultNpsRange::class,
            'minimal' => MinimalNpsRange::class,
            'emoji' => EmojiNpsRange::class,
            'text' => false,
            'why' => false,
        ];

        if($range[$this->range]):
            $values = config(
                "nps.range.{$this->range}", /** @phpstan-ignore-line */
                DefaultNpsRange::class
            ); return app($values)->toArray();
        else:
            return [];
        endif;

    }
}
