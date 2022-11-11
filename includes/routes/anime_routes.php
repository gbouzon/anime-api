<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../routes/base_routes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/AnimeModel.php';

// get routes: getAllAnime, getAnimeById, getAnimeReviews

//filtering allowed by: title, genre, studio name
function getAllAnime(Request $request, Response $response, array $args) {
   
}

// filters: anime name, username, genre
function getAnimeReviews(Request $request, Response $response, array $args) {
  
}

// Callback for HTTP GET /artists/{artist_id}
// Returns the artist with the specified ID.
function getAnimeById(Request $request, Response $response, array $args) {
    
}
