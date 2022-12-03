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
$api_anime_search = "https://gogoanime.consumet.org/search?keyw=";
$api_anime_details = "https://gogoanime.consumet.org/anime-details/";

//$clientSearch = new GuzzleHttp\Client(['base_uri' => $api_anime_search]);
//$clientDetails = new GuzzleHttp\Client(['base_uri' => $api_anime_details]);


//filtering allowed by: name, description, year, studio name, studio id
function getAllAnime(Request $request, Response $response, array $args) {
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
    else if (isset($filter_params["name"])) {

        //check if exists in db so:
            //CHECK WHAT THIS RETURNS WHEN NO MATCH IS FOUND *******
        if ($anime_model->getAnimeByName($filter_params['name'])) {
            //check if has cover picture
            //before, think about:
                //getAnimeByName uses LIKE with name in between %. It may return a list of anime that contain a certain keyword
                //we'd have to get a perfect match taking into consideration lower() and upper() in order for this to work
                //otherwise we'd just be adding the wrong cover picture to the anime record.
        }
        $clientSearch = new GuzzleHttp\Client(['base_uri' => "https://gogoanime.consumet.org/"]);
        $anime = searchForAnime($clientSearch, "search?keyw=" . $filter_params["name"]);
        //return list of anime, we grab always first result, anime id to get details
        $animeNameId = $anime[0]->animeId;
        $clientDetails = new GuzzleHttp\Client(['base_uri' => "https://gogoanime.consumet.org/anime-details/"]);
        $anime = getAnimeDetails($clientDetails, $animeNameId);
        $anime_model->insertAnime($anime->animeTitle, $anime->synopsis, $anime->releasedDate, $anime->totalEpisodes, $anime->animeImg);

        //in our db we have: anime_id, production_id name, description, year, nb_releases, cover_picture
        
        //$anime = $anime_model->getAnimeByName($filter_params["name"]);
    }
    else if (isset($filter_params['description'])) {
        $anime = $anime_model->getAnimeByDescription($filter_params['description']);
    }
    else if (isset($filter_params['year'])) {
        $anime = $anime_model->getAnimeByYear($filter_params['year']);
    }
    else {
        // No filtering by genre name or description detected.
        $anime = $anime_model->getAll();
    }

    return checkRepresentation($request, $response, $anime);
}

function getAnimeByStudio(Request $request, Response $response, array $args) {
    $anime = array();
    $response_data = array();
    $anime_model = new AnimeModel();

    $anime = $anime_model->getAnimeByStudio($args["studio_id"]);
    return checkRepresentation($request, $response, $anime);
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
    return $response->withStatus(200);
}

function updateAnime (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $anime_model = new AnimeModel();
    $response_code = HTTP_OK;

    if ($data) {
        for ($index = 0; $index < count($data); $index++) {
            $single_anime = $data[$index];
            $animeId = $single_anime['anime_id'];
            $existing_anime_record = array(
                "production_id" => $single_anime['production_id'],
                "name" => $single_anime['name'],
                "description" => $single_anime['description'],
                "year" => $single_anime['year'],
                "nb_releases" => $single_anime['nb_releases'],
                "cover_picture" => $single_anime['cover_picture'],
            );
            $anime_model->updateAnime($existing_anime_record, $animeId); // DO SQL
        }
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }
}
