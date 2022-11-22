<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../routes/base_routes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/UserModel.php';


function getAllUsers(Request $request, Response $response, array $args) {
    $users = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $user_model = new UserModel();

    $filter_params = $request->getQueryParams();
    if (isset($filter_params['userid'])) {
        $users = $user_model->getUserById($filter_params["userid"]);
    } 
    else if (isset($filter_params['username'])) {
        $users = $user_model->getUserByUsername($filter_params["username"]);
    }
    else if (isset($filter_params['reviewid'])) {
        $users = $user_model->getUserByReviewID($filter_params['reviewid']);
    }
    else {
        $users = $user_model->getAll();
    }
    return checkRepresentation($request, $response, $users);
}

function getUserMangaWatched(Request $request, Response $response, array $args) {
    $manga_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $manga_info = $user_model->getUserMangaWatched($user_id);
        return checkData($manga_info, $response, $request);
    }
    return unsupportedOperation($request, $response); 
}


function getUserMangaToWatch(Request $request, Response $response, array $args) {
    $manga_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $manga_info = $user_model->getUserMangaToWatch($user_id);
        return checkData($manga_info, $response, $request);
    }
    return unsupportedOperation($request, $response); 
}

function getUserAnimeWatched(Request $request, Response $response, array $args) {
    $anime_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $anime_info = $user_model->getUserAnimeWatched($user_id);
        return checkData($anime_info, $response, $request);
    }
    return unsupportedOperation($request, $response); 
}

function getUserAnimeToWatch(Request $request, Response $response, array $args) {
    $anime_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $anime_info = $user_model->getUserAnimeToWatch($user_id);
        return checkData($anime_info, $response, $request);
    }
    return unsupportedOperation($request, $response); 
}

 


