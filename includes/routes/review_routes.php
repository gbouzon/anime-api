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
    $filter_params = $request->getQueryParams();
    if(isset($filter_params['star_rating']))
        $reviews = $review_model->getReviewsByRate($filter_params['star_rating']);
    else if(isset($filter_params['date_before'])){
        if(!validateDate($filter_params['date_before'])){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Incorrect Date format. Should be 'YYYY-MM-DD' ");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }
        $reviews = $review_model->getReviewsBeforeDate($filter_params['date_before']);
    } else if(isset($filter_params['date_after'])){
        if(!validateDate($filter_params['date_after'])){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Incorrect Date format. Should be 'YYYY-MM-DD' ");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }
        $reviews = $review_model->getReviewsAfterDate($filter_params['date_after']);
    } else if (isset($filter_params['date_on'])){
        if(!validateDate($filter_params['date_on'])){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Incorrect Date format. Should be 'YYYY-MM-DD' ");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }
        $reviews = $review_model->getReviewsOnDate($filter_params['date_on']);
    }   
    else
        $reviews = $review_model->getAll();
    return checkdata($reviews, $response, $request);
}

function getUserReviews(Request $request, Response $response, array $args) {
    $reviews = array();
    $response_data = array();
    $review_model = new ReviewModel();

    // Retrieve the review if from the request's URI.
    $review_id= $args["review_id"];
    if (isset($review_id)) {
        // Fetch the info about the specified review.
        $reviews = $review_model->getUserReviews($review_id);
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
        if(isset($filter_params['star_rating']))
            $reviews_info = $review_model->getAnimeReviewsByRate($anime_id, $filter_params['star_rating']);
        else
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

    for ($index = 0; $index < count($data); $index++){
        $new_reviews_record = array();
        $single_review = $data[$index];
        // To-Do: data can not be null
        foreach($single_review as $property => $value){
            if($property == "user_id" || $property == "title" || $property == "star_rating"){
                if(empty($value)){
                    $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "$property property can not be null");
                    return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
                }
            }    
        } 

        // check anime_id or manga_id isset and if its exist
        if(isset($single_review["anime_id"]) && isset($single_review["manga_id"])){
            $manga_id = $single_review["manga_id"];
            $anime_id = $single_review["anime_id"];
            if(!$manga_model->getMangaById($manga_id) || !$anime_model->getAnimeById($anime_id)){
                $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific anime or manga does not exist.");
                return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response); 
            }
            $new_reviews_record["anime_id"] = $anime_id;
            $new_reviews_record["manga_id"] = $manga_id;
        }else if(isset($single_review["anime_id"])){
            if(!$anime_model->getAnimeById($single_review["anime_id"])){
                $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific anime does not exist.");
                return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response); 
            }
            $new_reviews_record["anime_id"] = $single_review["anime_id"];
        }else if(isset($single_review["manga_id"])){
            if(!$manga_model->getMangaById($single_review["manga_id"])){
                $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific manga does not exist.");
                return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response); 
            }
            $new_reviews_record["manga_id"] = $single_review["manga_id"];
        }else
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response); 
        
        //check user exited
        if(!$user_model->getUserById($single_review["user_id"])){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The assigne User do not exist.");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response); 
        }

        
         //check start_rating(between 1 to 0)
        if(isset($single_review["star_rating"])){
            $star_rating =  $single_review["star_rating"];
            if($star_rating > 5 || $single_review < 0){
                $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "star_rating need to be between 0 and 5");
                return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
            }
        } 
        

        $new_reviews_record = array_merge($new_reviews_record, 
            array("user_id" => $single_review["user_id"],
            "title" => $single_review["title"],
            "star_rating" => $single_review["star_rating"],
            "content" => $single_review["content"])
        );

        $query_result = $review_model->createReviews($new_reviews_record);
        if(!$query_result){
            return response(httpMethodNotAllowed(), HTTP_METHOD_NOT_ALLOWED, $response);
        }
        
    }
      
    return response(httpCreated(), HTTP_CREATED, $response);
}

function deleteReviews(Request $request, Response $response,  array $args) {
    $review_model = new ReviewModel();
    $parsed_data = $request->getParsedBody();
    $response_code = HTTP_OK;
    $review_id = $args["review_id"];

    if(isset($review_id)){
        if(!$review_model->getReviewById($review_id)){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific review do not existed");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);       
        }

        $query_result = $review_model->deleteReviews($review_id);
        if (!$query_result) {
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific review can not be delete");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }
    } else {
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The review_id need to be specify");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    return response(getSuccessDeleteMessage(), HTTP_OK, $response);
}

