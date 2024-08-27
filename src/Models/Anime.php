<?php

namespace App\Models;

use App\Controllers\DemographicsController;
use App\Validators\Validator;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

#[\AllowDynamicProperties]
class Anime extends Model
{
    protected $table = "anime";
    protected $fillable = [
        'id',
        'mal_id',
        'url',
        'images',
        'trailer',
        'approved',
        'title',
        'title_english',
        'title_japanese',
        'type',
        'source',
        'episodes_counter',
        'status',
        'airing',
        'aired',
        'duration',
        'rating',
        'score',
        'scored_by',
        'rank',
        'popularity',
        'members',
        'favorites',
        'synopsis',
        'background',
        'season',
        'year',
        'broadcast',
        'theme',
        'external',
        'streaming',
        'updated_at',
        'created_at',
        'active'
    ];

    protected $guarded = ['id'];
    public $allowed_keys = [
        'mal_id',
        'url',
        'images',
        'trailer',
        'approved',
        'title',
        'title_english',
        'title_japanese',
        'type',
        'source',
        'episodes_counter',
        'status',
        'airing',
        'aired',
        'duration',
        'rating',
        'score',
        'scored_by',
        'rank',
        'popularity',
        'members',
        'favorites',
        'synopsis',
        'background',
        'season',
        'year',
        'broadcast',
        'theme',
        'external',
        'streaming'
    ];

    public function titles()
    {
        return $this->hasMany(Titles::class);
    }

    public function titles_synonyms()
    {
        return $this->hasMany(TitlesSynonyms::class);
    }

    public function producers()
    {
        return $this->belongsToMany(Producers::class, 'relation_producer_anime', 'anime_id', 'producer_id');
    }

    public function licensors()
    {
        return $this->belongsToMany(Licensors::class, 'relation_licensor_anime', 'anime_id', 'licensor_id');
    }

    public function studios()
    {
        return $this->belongsToMany(Studios::class, 'relation_studio_anime', 'anime_id', 'studio_id');
    }

    public function genres()
    {
        return $this->belongsToMany(Genres::class, 'relation_genre_anime', 'anime_id', 'genre_id');
    }

    public function explicit_genres()
    {
        return $this->belongsToMany(ExplicitGenres::class, 'relation_explicit_genre_anime', 'anime_id', 'explicit_genre_id');
    }

    public function themes()
    {
        return $this->belongsToMany(Themes::class, 'relation_theme_anime', 'anime_id', 'theme_id');
    }

    public function demographics()
    {
        return $this->belongsToMany(Demographics::class, 'relation_demographic_anime', 'anime_id', 'demographic_id');
    }

    public function characters()
    {
        return $this->belongsToMany(Characters::class, 'anime_characters_staff', 'anime_id', 'character_id')->withPivot('language');
    }

    public function staff()
    {
        return $this->belongsToMany(Staff::class, 'anime_characters_staff', 'anime_id', 'staff_id')->withPivot('language');
    }

    /**
     * @throws Exception
     */
    public function validate(): bool
    {
        if (empty($this->token)) {
            $this->token = Uuid::uuid4();
        }

        return Validator::validateAnime($this);
    }

}
