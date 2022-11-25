<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/ReviewModel.php';


function getAllReviews(Request $request, Response $response, array $args) {
    $reviews = array();
    $response_data = array();
    $review_model = new ReviewModel();
    $reviews = $review_model->getAll();
    return checkRepresentation($request, $response, $reviews);
}

function getreviewReviews(Request $request, Response $response, array $args) {
    $reviews = array();
    $response_data = array();
    $review_model = new ReviewModel();

    // Retrieve the review if from the request's URI.
    $review_id= $args["review_id"];
    if (isset($review_id)) {
        // Fetch the info about the specified review.
        $reviews = $review_model->getreviewReviews($review_id);
        return checkData($reviews, $response, $request);
    }
    return unsupportedOperation($request, $response);
}

function getAnimeReviews(Request $request, Response $response, array $args) {
    $reviews_info = array();
    $response_data = array();
    $response_code = HTTP_OK;
    $review_model = new ReviewModel();

    $filter_params = $request->getQueryParams();
    // Retrieve the review if from the request's URI.
    $anime_id= $args["anime_id"];
    if (isset($anime_id)) {
        // Fetch the info about the specified review.
        $reviews_info = $review_model->getAnimeReviews($anime_id);
        return checkData($reviews_info, $response, $request);
    }
    return unsupportedOperation($request, $response); 
}

function getMangaReviews(Request $request, Response $response, array $args) {
    $reviews_info = array();
    $response_data = array();
    $review_model = new ReviewModel();

    $filter_params = $request->getQueryParams();
    // Retrieve the review if from the request's URI.
    $manga_id= $args["manga_id"];
    if (isset($manga_id)) {
        if(isset($filter_params['star_rating']))
            $reviews_info = $review_model->getMangaReviewsByRate($manga_id, $filter_params['star_rating']);
        else 
            // Fetch the info about the specified review.
            $reviews_info = $review_model->getMangaReviews($manga_id);
        return checkData($reviews_info, $response, $request);
    }
    return httpMethodNotAllowed(); 
}

/**
 * Callback for HTTP POST /reviews
 * add one or more reviews  (resource URI: /reviews)
 */
function createReviews(Request $request, Response $response, array $args){
    $data = $request->getParsedBody();
    $review_model = new ReviewModel();
    $manga_model = new MangaModel();
    $anime_model = new AnimeModel();
    $user_model = new UserModel();

    for ($index =0; $index < count($data); $index++){
        $new_reviews_record = array();
        $single_review = $data[$index];
        // To-Do: data can not be null 

        // check anime_id or manga_id isset and if its exist
        if(isset($single_review["anime_id"]) && isset($single_review["manga_id"])){
            $manga_id = $single_review["manga_id"];
            $anime_id = $single_review["anime_id"];
            //if()
            if(!$manga_model->getMangaById($manga_id) || !$anime_model->getAnimeById($anime_id)){
                return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); // can be better error code
            }
            $new_reviews_record["anime_id"] = $anime_id;
            $new_reviews_record["manga_id"] = $manga_id;
        }else if(isset($single_review["anime_id"])){
            if(!$anime_model->getAnimeById($single_review["anime_id"])){
                return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); // can be better error code
            }
            $new_reviews_record["anime_id"] = $single_review["anime_id"];
        } 
        else if(isset($single_review["manga_id"])){
            if(!$manga_model->getMangaById($single_review["manga_id"])){
                return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); // can be better error code
            }
            $new_reviews_record["manga_id"] = $single_review["manga_id"];
        }    
        else
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); // can be better error code
        
        //check user exited
        if(!$user_model->getUserById($single_review["user_id"]))
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); // can be better error code
        
         //check start_rating(between 1 to 0)
        $start_rating =  $single_review["star_rating"];
        if($start_rating > 5 || $single_review < 0){
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response);
        }

        $new_reviews_record = array_merge($new_reviews_record, 
            array("user_id" => $single_review["user_id"],
            "title" => $single_review["title"],
            "star_rating" => $single_review["star_rating"],
            "content" => $single_review["content"])
        );

        $review_model->createReviews($new_reviews_record);
    }
      
    return response(httpCreated(), HTTP_CREATED, $response);
}

