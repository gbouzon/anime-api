<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/MangaModel.php';

/**
 * Gets all manga (GET /manga)
 * Allows filtering by manga_id, name, mangaka
 */
function getAllManga(Request $request, Response $response, array $args) {
    $manga = array();
    $response_data = array();
    $manga_model = new MangaModel();

    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);
    if ($input_page_number == null) 
        $input_page_number = 1;
    if ($input_per_page == null)
        $input_per_page = 10;
    $manga_model->setPaginationOptions($input_page_number, $input_per_page);

    $filter_params = $request->getQueryParams();
    if (isset($filter_params['manga_id'])) {
        $manga = $manga_model->getMangaById($filter_params["manga_id"]);
    } 
    else if (isset($filter_params['name'])) {
        $manga = $manga_model->getMangaByName($filter_params["name"]);
    }
    else if (isset($filter_params['mangaka'])) {
        $manga = $manga_model->getByMangaka($filter_params['mangaka']);
    }
    else if (isset($filter_params['mangaka']) && isset($filter_params['name'])) {
        $manga = $manga_model->getByTitleMangaka($filter_params['mangaka'], $filter_params['name']);
    }
    else {
        $manga = $manga_model->getAll();
    }
    return checkData($manga, $response, $request);
}

/**
 * Inserts new manga record into the database (POST /manga)
 */
function createManga(Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();
    $manga_model = new MangaModel();
    $manga_info = array();

    for ($index = 0; $index < count($data); $index++) {
        $single_manga = $data[$index];
        foreach($single_manga as $property => $value){
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
        $mangaId = $single_manga['manga_id'];
        if ($manga_model->doesMangaIdExist($mangaId))
            $response->getBody()->write(makeCustomJSONError("resourceAlreadyExists", "The specified anime with id '$mangaId' already exists."));
        else {
            $new_manga_record = array(
                "manga_id" => $mangaId,
                "name" => $single_manga['name'],
                "description" => $single_manga['description'],
                "year" => $single_manga['year'],
                "mangaka" => $single_manga['mangaka'],
                "num_of_volumes" => $single_manga['num_of_volumes'],
                "cover_picture" => $single_manga['cover_picture'],
            );
            $manga_model->createManga($new_manga_record); 
            $response->getBody()->write(json_encode($new_manga_record));
        }
    }
    return $response->withStatus(201);
}

/**
 * Updates a manga record from the database (PUT /manga)
 */
function updateManga (Request $request, Response $response, $args) {
    $data = $request->getParsedBody();
    $manga_model = new MangaModel();
    $response_code = HTTP_OK;
    $response_data = array();

    if ($data) {
        for ($index = 0; $index < count($data); $index++) {
            $single_manga = $data[$index];
            $mangaId = $single_manga['manga_id'];
            foreach($single_manga as $property => $value){
                if ($property != "manga_id") {
                    if(!empty($value))
                        $response_data += array($property => $value);
                }
            }
            $manga_model->updateManga($response_data, $mangaId); // DO SQL
        }
        $response->getBody()->write(json_encode($data));
        return $response->withStatus(200);
    }
}