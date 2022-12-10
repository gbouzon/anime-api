<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/StudioModel.php';

/**
 * Gets all studios (GET /studios)
 */
function getAllStudios(Request $request, Response $response, array $args) {
    $studios = array();
    $response_data = array();
    $studio_model = new StudioModel();

    $input_page_number = filter_input(INPUT_GET, "page", FILTER_VALIDATE_INT);
    $input_per_page = filter_input(INPUT_GET, "per_page", FILTER_VALIDATE_INT);
    if ($input_page_number == null) 
        $input_page_number = 1;
    if ($input_per_page == null)
        $input_per_page = 10;
    $studio_model->setPaginationOptions($input_page_number, $input_per_page);

    $filter_params = $request->getQueryParams();
    if (isset($filter_params['studio_id'])) {
        $studios = $studio_model->getStudioById($filter_params["studio_id"]);
    } 
    else if (isset($filter_params['name'])) {
        $studios = $studio_model->getStudioByName($filter_params["name"]);

    } else if(isset($filter_params['anime_title'])){
        $studios = $studio_model->getStudiobyAnimeTitle($filter_params['anime_title']);
    } else if(isset($filter_params['anime_id'])){
        $studios = $studio_model->getStudiobyAnimeId($filter_params['anime_id']);
    } else {
        $studios = $studio_model->getAll();
    }
    return checkData($studios, $response, $request);
}