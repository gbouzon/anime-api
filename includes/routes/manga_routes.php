<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/MangaModel.php';


function getAllManga(Request $request, Response $response, array $args) {
    $manga = array();
    $response_data = array();
    $manga_model = new MangaModel();

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
    return checkRepresentation($request, $response, $manga);
}
