<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

//for composite resources
use GuzzleHttp\Client as Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;    
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

require __DIR__ . './../../vendor/autoload.php';
require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/AnimeModel.php';

//FOR COMPOSITE RESOURCES
$api_anime_search = "https://gogoanime.consumet.org/";
$api_anime_details = "https://gogoanime.consumet.org/anime-details/";

$clientSearch = new GuzzleHttp\Client(['base_uri' => $api_anime_search]);
$clientDetails = new GuzzleHttp\Client(['base_uri' => $api_anime_details]);


//filtering allowed by: name, description, year, studio name, studio id
function getAllAnime(Request $request, Response $response, array $args) {
    global $clientSearch, $clientDetails, $api_anime_search, $api_anime_details;
    $anime = array();
    $response_data = array();
    $anime_model = new AnimeModel();

    // Retrieve the query string parameter from the request's URI.
    $filter_params = $request->getQueryParams();
    if (isset($filter_params['anime_id'])) { //adding get anime by id through query strings instead uri
        $manga = $manga_model->getAnimeById($filter_params["anime_id"]);
    }
    else if (isset($filter_params['studio_name'])) {
        $anime = $anime_model->getAnimeByStudioName($filter_params["studio_name"]);
    }
    else if (isset($filter_params['studio_id'])) {
        $anime = $anime_model->getAnimeByStudio($filter_params['studio_id']);
    }
    else if (isset($filter_params['name']) && isset($filter_params['description']) && isset($filter_params['year'])) {
        // Fetch the list of genres matching the provided name.
        $anime = $anime_model->getByNameDescriptionYear($filter_params["name"], $filter_params['description'], $filter_params['year']);
    } 
    else if (isset($filter_params['name']) && isset($filter_params['description'])) {
        $anime = $anime_model->getByNameDescription($filter_params["name"], $filter_params['description']);
    }
    else if (isset($filter_params['name']) && isset($filter_params['year'])) {
        $anime = $anime_model->getByNameYear($filter_params["name"], $filter_params['year']);
    }
    else if (isset($filter_params['description']) && isset($filter_params['year'])) {
        $anime = $anime_model->getByDescriptionYear($filter_params['description'], $filter_params['year']);
    }
    //user types in a name, example: your lie in april
    //check the database first and see if there's a record with that name
    //if not, query the api for that name
    //returns name as Shigatsu whatevers
    //if the name is not the same as user input, check db again for that name
    //if it is in the db, add input as other_name and return the record
    //if not, check if the name is the same as user typed in, query the animedetails api for the id of that anime
    //add it to db and return the record
    else if (isset($filter_params["name"])) {
        $anime = $anime_model->getAnimeByName($filter_params["name"]);
        //if there's no anime by that name, query api and add to database
        if (empty($anime)) {
            $anime_search = searchForAnime($clientSearch, "search?keyw=" . $filter_params["name"]);
            if (!empty($anime_search)) {
                $animeNameId = $anime_search[0]->animeId;
                $animeTitle = $anime_search[0]->animeTitle;
                if ($animeTitle.lower() != $filter_params["name"].lower()) {
                    $new_anime = $anime_model->getAnimeByName($filter_params["name"]);
                    if (!empty($new_anime)) {
                        $new_anime = $anime_model->addOtherTitle($filter_params["name"], $new_anime_id);
                        $anime = $new_anime;
                    }
                    else {
                        $anime = getAnimeDetails($clientDetails, $animeNameId);
                        $animeOtherTitle = $anime->otherNames;
                        $anime_model->insertAnime($anime->animeTitle, $animeOtherTitle[0], $anime->synopsis, $anime->releasedDate, $anime->totalEpisodes, $anime->animeImg);


                    }
                }
                $anime = getAnimeDetails($clientDetails, $animeNameId);
                $animeOtherTitle = $anime->otherNames;
                if (!empty($animeOtherTitle)) {
                        $other = $anime_model->getAnimeByName($animeOtherTitle[0]);
                        if (!empty($other)) {
                            $anime = $other;
                        }
                        else {
                            $anime_model->addOtherTitle($animeOtherTitle[0], );
                            $anime_model->insertAnime($animeOtherTitle[0], $anime->synopsis, $anime->releasedDate, $anime->totalEpisodes, $anime->animeImg);
                            $anime = $anime_model->getAnimeById($anime_model->lastIdInsert());
                        }
                }
                if (!empty($anime_model->getAnimeByName($anime->animeTitle))) {
                    $anime = $anime_model->getAnimeByName($anime->animeTitle);
                }
                else {
                    $anime_model->insertAnime($anime->animeTitle, $anime->synopsis, $anime->releasedDate, $anime->totalEpisodes, $anime->animeImg);
                    $anime = $anime_model->getAnimeById($anime_model->lastIdInsert());
                } 
            }
        }
        
        else {
            //think if you want to add this because this is technically supposed to return a list of anime. either go through it one by one
            //if there is a match, check if it has cover picture. if not, add it
        }
    } 
    else if (isset($filter_params['description'])) {
        $anime = $anime_model->getByDescription($filter_params['description']);
    }
    else if (isset($filter_params['year'])) {
        $anime = $anime_model->getByYear($filter_params['year']);
    }
    else {
        // No filtering by genre name or description detected.
        $anime = $anime_model->getAll();
    }

    return checkData($anime, $response, $request);
}

function getAnimeByStudio(Request $request, Response $response, array $args) {
    $anime = array();
    $response_data = array();
    $anime_model = new AnimeModel();

    $anime = $anime_model->getAnimeByStudio($args["studio_id"]);
    return checkData($anime, $response, $request);
}

function searchForAnime(Client $client, $anime_name) {
    $response = $client->request('GET', $anime_name);
    $data = json_decode($response->getBody()->getContents());
    return $data;
}

function getAnimeDetails(Client $client, $anime_id) {
    $response = $client->request('GET', $anime_id);
    $data = json_decode($response->getBody()->getContents());
    return $data;
}

function createAnime(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $anime_model = new AnimeModel();
    $anime_info = array();

    for ($index = 0; $index < count($data); $index++) {
        $single_anime = $data[$index];
        foreach($single_anime as $property => $value){
            if ($property != "cover_picture") {
                if(empty($value)){
                    $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "$property property can not be null");
                    return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
                }
            }
            else {
                if (empty($value)) {
                    $value = "blank.jpg";
                }
            }
        }
        $animeId = $single_anime['anime_id'];
        if ($anime_model->doesAnimeIdExist($animeId))
            $response->getBody()->write(makeCustomJSONError("resourceAlreadyExists", "The specified anime with id '$animeId' already exists."));
        else {
            $new_anime_record = array(
                "anime_id" => $animeId,
                "production_id" => $single_anime['production_id'],
                "name" => $single_anime['name'],
                "description" => $single_anime['description'],
                "year" => $single_anime['year'],
                "nb_releases" => $single_anime['nb_releases'],
                "cover_picture" => $single_anime['cover_picture'],

            );
            $anime_model->createAnime($new_anime_record); 
            $response->getBody()->write(json_encode($new_anime_record));
        }
    }
    return $response->withStatus(HTTP_CREATED);
}

function updateAnime (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $anime_model = new AnimeModel();
    $response_code = HTTP_OK;
    $response_data = array();

    if ($data) {
        for ($index = 0; $index < count($data); $index++) {
            $single_anime = $data[$index];
            foreach($single_anime as $property => $value){
                if ($property != "anime_id") {
                    if(!empty($value))
                        $response_data += array($property => $value);
                }
            }
            $animeId = $single_anime['anime_id'];
            $anime_model->updateAnime($response_data, $animeId); // DO SQL
        }
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }
}
