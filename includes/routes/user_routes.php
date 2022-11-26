<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/UserModel.php';


function getAllUsers(Request $request, Response $response, array $args) {
    $users = array();
    $response_data = array();
    $user_model = new UserModel();

    $filter_params = $request->getQueryParams();
    if (isset($filter_params['user_id'])) {
        $users = $user_model->getUserById($filter_params["user_id"]);
    } 
    else if (isset($filter_params['username'])) {
        $users = $user_model->getUserByUsername($filter_params["username"]);
    }
    else if (isset($filter_params['review_id'])) {
        $users = $user_model->getUserByReviewID($filter_params['review_id']);
    }
    else {
        $users = $user_model->getAll();
    }
    return checkRepresentation($request, $response, $users);
}

function getUserMangaWatched(Request $request, Response $response, array $args) {
    $manga_info = array();
    $response_data = array();
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $manga_info = $user_model->getUserMangaWatched($user_id);
        return checkData($manga_info, $response, $request);
    }
    return httpMethodNotAllowed(); 
}

function getUserMangaToWatch(Request $request, Response $response, array $args) {
    $manga_info = array();
    $response_data = array();
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $manga_info = $user_model->getUserMangaToWatch($user_id);
        return checkData($manga_info, $response, $request);
    }
    return httpMethodNotAllowed(); 
}

function getUserAnimeWatched(Request $request, Response $response, array $args) {
    $anime_info = array();
    $response_data = array();
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $anime_info = $user_model->getUserAnimeWatched($user_id);
        return checkData($anime_info, $response, $request);
    }
    return httpMethodNotAllowed(); 
}

function getUserAnimeToWatch(Request $request, Response $response, array $args) {
    $anime_info = array();
    $response_data = array();
    $user_model = new UserModel();

    // Retrieve the user if from the request's URI.
    $user_id= $args["user_id"];
    if (isset($user_id)) {
        // Fetch the info about the specified user.
        $anime_info = $user_model->getUserAnimeToWatch($user_id);
        return checkData($anime_info, $response, $request);
    }
    return httpMethodNotAllowed(); 
}

/**
 * Callback for HTTP POST /users
 * add one or more users  (resource URI: /users)
 */
function createUsers(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    $user_model = new UserModel();
    
    for ($index =0; $index < count($data); $index++){
        $single_user = $data[$index];

        // check if data is not null, () 
        foreach($single_user as $property => $value){
            if($property != "phone"){
                if(empty($value)){
                    $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "$property property can not be null");
                    return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
                }
            }    
        }

        //check if the user exist already 
        $checkExisteUserName = $user_model->getUserByUsername($single_user["username"]);
        $checkExisteEmail = $user_model->getUserByEmail($single_user["email"]);
        if($checkExisteUserName || $checkExisteEmail){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Username or Email need to be Unique");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }

        $new_users_record = array(
            "username" => $single_user["username"],
            "fname" => $single_user["fname"],
            "lname" => $single_user["lname"],
            "email" => $single_user["email"],
            "password_hash" => $single_user["password_hash"],
            "phone" => $single_user["phone"]
        );

        $query_result = $user_model->createUsers($new_users_record);
        if(!$query_result){
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response);
        }
        
    }
      
    return response(httpCreated(), HTTP_CREATED, $response);
}