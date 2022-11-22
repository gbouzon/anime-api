<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../routes/base_routes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/StudioModel.php';


function getAllStudios(Request $request, Response $response, $args) {
    $studios = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $studio_model = new StudioModel();

    $filter_params = $request->getQueryParams();
    if (isset($filter_params['studio_id'])) {
        $studios = $studio_model->getStudioById($filter_params["studio_id"]);
    } 
    else if (isset($filter_params['name'])) {
        $studios = $studio_model->getStudioByName($filter_params["name"]);
    }
    else {
        $studios = $studio_model->getAll();
    }
    return checkRepresentation($request, $response, $studios);
}
