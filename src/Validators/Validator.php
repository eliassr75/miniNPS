<?php

namespace App\Validators;

use App\Models\Characters;
use App\Models\Demographics;
use App\Models\ExplicitGenres;
use App\Models\Genres;
use App\Models\Licensors;
use App\Models\Positions;
use App\Models\RelationLicensorAnime;
use App\Models\Staff;
use App\Models\Studios;
use App\Models\Themes;
use App\Models\User;
use App\Models\Anime;
use App\Models\Titles;
use App\Models\TitlesSynonyms;
use App\Models\Producers;
use App\Models\RelationProducerAnime;
use Exception;

class Validator {

    public static function validateLog($log): bool
    {
        if (empty($log->title)) {
            throw new Exception("O Nome deve ser preenchido.");
        }
        if (empty($log->description)) {
            throw new Exception("O Email deve ser preenchido.");
        }

        return true;
    }

    public static function validateFormNewUser($form): bool
    {
        if (empty($form->name)) {
            throw new Exception("O Nome deve ser preenchido.");
        }
        if (empty($form->email)) {
            throw new Exception("O Email deve ser preenchido.");
        }
        if (empty($form->password)) {
            throw new Exception("As senhas devem ser preenchidas.");
        }
        if (empty($form->confirm_password)) {
            throw new Exception("As senhas devem ser preenchidas.");
        }

        return true;
    }

    public static function validateEmail($email): bool
    {
        $regex = '/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
        return preg_match($regex, $email);
    }
    public static function validateTitle(Titles $title): bool
    {
        if (empty($title->title)) {
            throw new Exception("O Título não pode ser nulo.");
        }
        if (empty($title->anime_id)) {
            throw new Exception("O Id do Anime não pode ser nulo.");
        }
        if (empty($title->type)) {
            throw new Exception("O Tipo não pode ser nulo.");
        }

        return true;
    }

    public static function validateTitleSynonyms(TitlesSynonyms $title): bool
    {
        if (empty($title->title)) {
            throw new Exception("O Título não pode ser nulo.");
        }
        if (empty($title->anime_id)) {
            throw new Exception("O Id do Anime não pode ser nulo.");
        }

        return true;
    }

    public static function validateProducers(Producers $producer): bool
    {
        if (empty($producer->type)) {
            throw new Exception("O Tipo do produtor não pode ser nulo.");
        }
        if (empty($producer->name)) {
            throw new Exception("O Nome do produtor não pode ser nulo.");
        }
        if (empty($producer->url)) {
            throw new Exception("A URL do produtor não pode ser nulo.");
        }
        if (empty($producer->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateLicensors(Licensors $producer): bool
    {
        if (empty($producer->type)) {
            throw new Exception("O Tipo do licenciador não pode ser nulo.");
        }
        if (empty($producer->name)) {
            throw new Exception("O Nome do licenciador não pode ser nulo.");
        }
        if (empty($producer->url)) {
            throw new Exception("A URL do licenciador não pode ser nulo.");
        }
        if (empty($producer->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateStudios(Studios $studio): bool
    {
        if (empty($studio->type)) {
            throw new Exception("O Tipo do studio não pode ser nulo.");
        }
        if (empty($studio->name)) {
            throw new Exception("O Nome do studio não pode ser nulo.");
        }
        if (empty($studio->url)) {
            throw new Exception("A URL do studio não pode ser nulo.");
        }
        if (empty($studio->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateGenres(Genres $genre): bool
    {
        if (empty($genre->type)) {
            throw new Exception("O Tipo do genero não pode ser nulo.");
        }
        if (empty($genre->name)) {
            throw new Exception("O Nome do genero não pode ser nulo.");
        }
        if (empty($genre->url)) {
            throw new Exception("A URL do genero não pode ser nulo.");
        }
        if (empty($genre->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateExplicitGenres(ExplicitGenres $genre): bool
    {
        if (empty($genre->type)) {
            throw new Exception("O Tipo do genero explícito não pode ser nulo.");
        }
        if (empty($genre->name)) {
            throw new Exception("O Nome do genero explícito não pode ser nulo.");
        }
        if (empty($genre->url)) {
            throw new Exception("A URL do genero explícito não pode ser nulo.");
        }
        if (empty($genre->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateThemes(Themes $themes): bool
    {
        if (empty($themes->type)) {
            throw new Exception("O Tipo do tema não pode ser nulo.");
        }
        if (empty($themes->name)) {
            throw new Exception("O Nome do tema não pode ser nulo.");
        }
        if (empty($themes->url)) {
            throw new Exception("A URL do tema não pode ser nulo.");
        }
        if (empty($themes->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateDemographics(Demographics $demographic): bool
    {
        if (empty($demographic->type)) {
            throw new Exception("O Tipo da demografia não pode ser nula.");
        }
        if (empty($demographic->name)) {
            throw new Exception("O Nome da demografia não pode ser nula.");
        }
        if (empty($demographic->url)) {
            throw new Exception("A URL da demografia não pode ser nula.");
        }
        if (empty($demographic->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateCharacter(Characters $character): bool
    {
        if (empty($character->images)) {
            throw new Exception("As imagens do personagem não podem ser nula.");
        }
        if (empty($character->name)) {
            throw new Exception("O Nome do personagem não pode ser nulo.");
        }
        if (empty($character->url)) {
            throw new Exception("A URL da personagem não pode ser nula.");
        }
        if (empty($character->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validateStaff(Staff $staff): bool
    {
        if (empty($staff->images)) {
            throw new Exception("As imagens do ator não podem ser nula.");
        }
        if (empty($staff->name)) {
            throw new Exception("O Nome do ator não pode ser nulo.");
        }
        if (empty($staff->url)) {
            throw new Exception("A URL da ator não pode ser nula.");
        }
        if (empty($staff->mal_id)) {
            throw new Exception("O MAL ID não pode ser nulo.");
        }

        return true;
    }

    public static function validatePositions(Positions $staff): bool
    {
        if (empty($staff->name)) {
            throw new Exception("O Nome do cargo não pode ser nulo.");
        }
        return true;
    }

    public static function validateUser(User $user): bool
    {
        if (empty($user->name)) {
            throw new Exception("O nome não pode ser nulo.");
        }
        if (empty($user->email)) {
            throw new Exception("O email não pode ser nulo.");
        }
        if (!filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Formato de email inválido.");
        }
        if (empty($user->password)) {
            throw new Exception("A senha não pode ser nula.");
        }
        if (empty($user->username)) {
            throw new Exception("O username é obrigatório.");
        }

        return true;
    }

    public static function validateAnime(Anime $anime): bool
    {

        if (empty($anime->title)) {
            throw new Exception("O nome padrão não pode ser nulo.");
        }
//        if(!is_array($anime->titles)){
//            throw new Exception("Os títulos devem ser um array.");
//        }
//        if(!is_array($anime->title_synonyms)){
//            throw new Exception("Os sinônimos de titulos devem ser um array.");
//        }
//        if(!is_array($anime->producers)){
//            throw new Exception("Os produtores devem ser um array.");
//        }
//        if(!is_array($anime->licensors)){
//            throw new Exception("Os licenciadores devem ser um array.");
//        }
//        if(!is_array($anime->studios)){
//            throw new Exception("Os studios devem ser um array.");
//        }
//        if(!is_array($anime->genres)){
//            throw new Exception("Os gêneros devem ser um array.");
//        }
//        if(!is_array($anime->explicit_genres)){
//            throw new Exception("Os gêneros explícitos devem ser um array.");
//        }
//        if(!is_array($anime->themes)){
//            throw new Exception("Os temas devem ser um array.");
//        }
//        if(!is_array($anime->demographics)){
//            throw new Exception("As demografias devem ser um array.");
//        }
//        if(!is_array($anime->relations)){
//            throw new Exception("As relações devem ser um array.");
//        }
//        if(!is_array($anime->external)){
//            throw new Exception("As informações externas devem ser um array.");
//        }
//        if(!is_array($anime->streaming)){
//            throw new Exception("As informações de streaming devem ser um array.");
//        }

        return true;
    }
}
?>
