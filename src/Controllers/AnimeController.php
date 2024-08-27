<?php

namespace App\Controllers;

use App\Models\Anime;
use App\Models\ExplicitGenres;
use App\Models\Genres;
use Exception;
use Statickidz\GoogleTranslate;

class AnimeController extends BaseController
{

    protected $url = "https://api.jikan.moe/v4/";
    public $query = null;


    public function getAllFilters()
    {

        return [
            [
                "type_filter" => "select",
                "filters" => [
                    "name" => "anime_search_query_type",
                    "values" => [
                        ["name" => "tv", "label" => "TV"],
                        ["name" => "tv_special", "label" => "TV Especial"],
                        ["name" => "movie", "label" => "Filme"],
                        ["name" => "ova", "label" => "OVA"],
                        ["name" => "ona", "label" => "ONA"],
                        ["name" => "special", "label" => "Especial"],
                        ["name" => "music", "label" => "Musical"],
                        ["name" => "cm", "label" => "CM"],
                        ["name" => "pv", "label" => "PV"],
                    ]
                ],
                [
                    "name" => "top_anime_filter",
                    "values" => [
                        ["name" => "airing", "label" => "Em Lançamento"],
                        ["name" => "upcoming", "label" => "Em Breve"],
                        ["name" => "bypopularity", "label" => "Mais Populares"],
                        ["name" => "favorite", "label" => "Favoritos"],
                    ]
                ],
                [
                    "name" => "anime_search_query_rating",
                    "values" => [
                        ["name" => "g", "label" => "Todas as Idades"],
                        ["name" => "pg", "label" => "Infantis"],
                        ["name" => "pg13", "label" => "Adolescentes com 13 anos ou mais"],
                        ["name" => "r17", "label" => "Violência e Profanidade"],
                        ["name" => "r", "label" => "Nudez Leve"],
                        ["name" => "rx", "label" => "Hentai"],
                    ]
                ]
            ],
            [
                "type_filter" => "radio",
                "filters" => [
                    "name" => "sfw",
                    "label" => "Exibir conteúdo adulto",
                    "values" => [
                        ["name" =>"Sim", "value" => 0],
                        ["name" =>"Não", "value" => 1]
                    ]
                ]
            ]
            ,
            "page" => [],
            "limit" => []
        ];
    }

    public function getAnimeById($animeId) {
        echo "Anime ID: " . htmlspecialchars($animeId, ENT_QUOTES, 'UTF-8');
    }
    public function getAllAnimes()
    {
        $functions = new FunctionController();
        $animes = Anime::all($this->pdo);
        echo '<pre>';
        print_r($animes);
        echo '</pre>';
    }

    public function getApiJikan($type="tv", $filter="bypopularity", $rating="g", $page=1, $limit=24)
    {
        $functions = new FunctionController();
        $parameters = [
            "anime_search_query_type" => $type,
            "top_anime_filter" => $filter,
            "anime_search_query_rating" => $rating,
            "sfw" => isset($_GET["sfw"]) ? intval($_GET["sfw"]) : 1,
            "page" => $page,
            "limit" => $limit
        ];

        $response = $functions->sendRequest($parameters, "{$this->url}{$this->query}", "get");
        if (!empty($response->error)) {
            return $functions->sendResponse($response, 500);
        } else {
            return $functions->sendResponse($response);
        }
    }

    public function createAnime($malId)
    {
        $this->query = "anime/$malId/full/";
        $functions = new FunctionController();
        $functions->api = true;

        $animeModel = new Anime();
        $anime = $animeModel->where('mal_id', $malId)->first();
        if ($anime) {
            $functions->sendResponse(["message" => "Anime already exists"], 400);
        } else {

            $response = $functions->sendRequest([], "{$this->url}{$this->query}", "get");
            foreach ($animeModel->allowed_keys as $key) {

                if ($key == "episodes_counter"){
                    $animeModel->$key = $response->data->episodes;
                }elseif($key == "external"){
                    $animeModel->$key = json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }elseif($key == "streaming"){
                    $animeModel->$key = json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }else{
                    switch (gettype($response->data->$key)) {
                        case 'boolean':
                            $animeModel->$key = (int)$response->data->$key;
                            break;
                        case 'object':
                            $animeModel->$key = json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                            break;
                        default:
                            $animeModel->$key = $response->data->$key;
                            break;
                    }
                }
            }

            $source = 'en';
            $target = 'pt-br';
            $translator = new GoogleTranslate();

            try {
                if(!empty($animeModel->background)){
                    $animeModel->background = str_replace("\n\n", "<br>", $translator->translate($source, $target, $animeModel->background));
                }
                if(!empty($animeModel->synopsis)) {
                    $animeModel->synopsis = str_replace("\n\n", "<br>", $translator->translate($source, $target, $animeModel->synopsis));
                }
            } catch (Exception $e) {

            }

            $animeModel->save();

            if (isset($response->data->titles)) {
                $titlesController = new TitlesController();
                $titlesController->anime_id = $animeModel->id;
                $titlesController->titles = $response->data->titles;
                $titlesController->checkTitles();
            }

            if (isset($response->data->title_synonyms)) {
                $titlesSynonymsController = new TitlesSynonymsController();
                $titlesSynonymsController->anime_id = $animeModel->id;
                $titlesSynonymsController->titles = $response->data->title_synonyms;
                $titlesSynonymsController->checkTitlesSynonyms();
            }

            if (isset($response->data->producers)) {
                $producersController = new ProducersController();
                $producersController->anime_id = $animeModel->id;
                $producersController->producers = $response->data->producers;
                $producersController->checkProducers();
            }

            if (isset($response->data->licensors)) {
                $licensorsController = new LicensorsController();
                $licensorsController->anime_id = $animeModel->id;
                $licensorsController->licensors = $response->data->licensors;
                $licensorsController->checkLicensors();
            }

            if (isset($response->data->studios)) {
                $studiosController = new StudiosController();
                $studiosController->anime_id = $animeModel->id;
                $studiosController->studios = $response->data->studios;
                $studiosController->checkStudios();
            }

            if (isset($response->data->genres)) {
                $genresController = new GenresController();
                $genresController->anime_id = $animeModel->id;
                $genresController->genres = $response->data->genres;
                $genresController->checkGenres();
            }

            if (isset($response->data->explicit_genres)) {
                $explicit_genresController = new ExplicitGenresController();
                $explicit_genresController->anime_id = $animeModel->id;
                $explicit_genresController->explicit_genres = $response->data->explicit_genres;
                $explicit_genresController->checkExplicitGenres();
            }

            if (isset($response->data->themes)) {
                $themesController = new ThemesController();
                $themesController->anime_id = $animeModel->id;
                $themesController->themes = $response->data->themes;
                $themesController->checkThemes();
            }

            if (isset($response->data->demographics)) {
                $demographicsController = new DemographicsController();
                $demographicsController->anime_id = $animeModel->id;
                $demographicsController->demographics = $response->data->demographics;
                $demographicsController->checkDemographics();
            }

            print_r($animeModel);

        }

    }

    public function updateAnime($malId)
    {
        $this->query = "anime/$malId/full/";
        $functions = new FunctionController();
        $functions->api = true;

        $animeModel = new Anime();
        $anime = $animeModel->where('mal_id', $malId)->get();
        if ($anime) {

            $response = $functions->sendRequest([], "{$this->url}{$this->query}", "get");
            $stmt = [];
            foreach ($animeModel->allowed_keys as $key) {

                if ($key == "episodes_counter"){
                    $stmt[$key] = $response->data->episodes;
                }elseif($key == "external"){
                    $animeModel->$key = json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }elseif($key == "streaming"){
                    $animeModel->$key = json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
                }else{
                    $stmt[$key] = match (gettype($response->data->$key)) {
                        'boolean' => (int)$response->data->$key,
                        'object' => json_encode($response->data->$key, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE),
                        default => $response->data->$key,
                    };
                }
            }

            $source = 'en';
            $target = 'pt-br';
            $translator = new GoogleTranslate();

            try {
                if(!empty($response->data->background)){
                    $stmt['background'] = str_replace("\n\n", "<br>", $translator->translate($source, $target, $anime->background));
                }
                if(!empty($response->data->synopsis)) {
                    $stmt['synopsis'] = str_replace("\n\n", "<br>", $translator->translate($source, $target, $anime->synopsis));
                }
            } catch (Exception $e) {

            }

            $animeModel->where('mal_id', $malId)->update($stmt);
            $anime = $animeModel->where('mal_id', $malId)->first();

            if (isset($response->data->titles)) {
                $titlesController = new TitlesController();
                $titlesController->anime_id = $anime->id;
                $titlesController->titles = $response->data->titles;
                $titlesController->checkTitles();
            }

            if (isset($response->data->title_synonyms)) {
                $titlesSynonymsController = new TitlesSynonymsController();
                $titlesSynonymsController->anime_id = $anime->id;
                $titlesSynonymsController->titles = $response->data->title_synonyms;
                $titlesSynonymsController->checkTitlesSynonyms();
            }

            if (isset($response->data->producers)) {
                $producersController = new ProducersController();
                $producersController->anime_id = $anime->id;
                $producersController->producers = $response->data->producers;
                $producersController->checkProducers();
            }

            if (isset($response->data->licensors)) {
                $licensorsController = new LicensorsController();
                $licensorsController->anime_id = $anime->id;
                $licensorsController->licensors = $response->data->licensors;
                $licensorsController->checkLicensors();
            }

            if (isset($response->data->studios)) {
                $studiosController = new StudiosController();
                $studiosController->anime_id = $anime->id;
                $studiosController->studios = $response->data->studios;
                $studiosController->checkStudios();
            }

            if (isset($response->data->genres)) {
                $genresController = new GenresController();
                $genresController->anime_id = $anime->id;
                $genresController->genres = $response->data->genres;
                $genresController->checkGenres();
            }

            if (isset($response->data->explicit_genres)) {
                $explicit_genresController = new ExplicitGenresController();
                $explicit_genresController->anime_id = $anime->id;
                $explicit_genresController->explicit_genres = $response->data->explicit_genres;
                $explicit_genresController->checkExplicitGenres();
            }

            if (isset($response->data->themes)) {
                $themesController = new ThemesController();
                $themesController->anime_id = $anime->id;
                $themesController->themes = $response->data->themes;
                $themesController->checkThemes();
            }

            if (isset($response->data->demographics)) {
                $demographicsController = new DemographicsController();
                $demographicsController->anime_id = $anime->id;
                $demographicsController->demographics = $response->data->demographics;
                $demographicsController->checkDemographics();
            }

            //Process Characters
            $characters = $this->getCharacters($malId);
            if(is_array($characters) and !empty($characters)){

                $charactersController = new CharactersController();
                $charactersController->anime_id = $anime->id;
                $charactersController->characters = $characters;

            }

            print_r($response->data);
            print_r($anime);

        } else {
            $functions->sendResponse(["message" => "Anime not founded"], 404);
        }

    }

    public function getCharacters($malId)
    {
        $this->query = "anime/$malId/characters";
        $functions = new FunctionController();
        $functions->api = true;

        try{
            $response_anime = $this->getApiJikan();
            if(isset($response_anime->data)){
                echo "<pre>";
                print_r($response_anime->data);
                echo "</pre>";
                return $response_anime->data;
            }else{
                $functions->sendResponse(["error" => "Characters for this Anime not founded"], 404);
            }

        }catch (Exception $e){
            $functions->sendResponse(["error" => $e->getMessage()]);
        }

    }

    public function getStaff($malId)
    {
        $this->query = "anime/$malId/staff";
        $functions = new FunctionController();
        $functions->api = true;

        try{
            $response_anime = $this->getApiJikan();
            if(isset($response_anime->data)){
                echo "<pre>";
                print_r($response_anime->data);
                echo "</pre>";
                return $response_anime->data;
            }else{
                $functions->sendResponse(["message" => "Staff for this Anime not founded"], 404);
            }


        }catch (Exception $e){
            return ["message" => $e->getMessage()];
        }
    }

    public function showAllAnimes()
    {
        define('TITLE_PAGE', 'TESTE');
        $this->render('template', ["login_page" => true]);
    }
}