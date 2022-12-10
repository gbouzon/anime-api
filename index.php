<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use GuzzleHttp\Client as Client;
use GuzzleHttp\Psr7\Request as GuzzleRequest;    
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Psr\Http\Message\ResponseInterface;
use GuzzleHttp\Exception\RequestException;

require __DIR__ . '/vendor/autoload.php';

//constants
require_once './includes/app_constants.php';
require_once './includes/helpers/helper_functions.php';
require_once './includes/helpers/JWTManager.php';

//routes
require_once './includes/routes/base_routes.php';
require_once './includes/routes/anime_routes.php';
require_once './includes/routes/manga_routes.php';
require_once './includes/routes/review_routes.php';
require_once './includes/routes/studio_routes.php';
require_once './includes/routes/user_routes.php';
require_once './includes/routes/genre_routes.php';
require_once './includes/routes/token_routes.php';

define('APP_BASE_DIR', __DIR__);
// IMPORTANT: This file must be added to your .ignore file. 
define('APP_ENV_CONFIG', 'config.env');


$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$api_base_path = "/webservices/anime-api";
$app->setBasePath($api_base_path);

$jwt_secret = JWTManager::getSecretKey();
$app->add(new Tuupola\Middleware\JwtAuthentication([
            'secret' => $jwt_secret,
            'algorithm' => 'HS256',
            'secure' => false,            
            "path" => $api_base_path, 
            "attribute" => "decoded_token_data",
            "ignore" => ["$api_base_path/token", "$api_base_path/account"],
            "error" => function ($response, $arguments) {
                $data["status"] = "error";
                $data["message"] = $arguments["message"];
                $response->getBody()->write(
                        json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT)
                );
                return $response->withHeader("Content-Type", "application/json;charset=utf-8");
            }
        ]));

        
// Routes for user account, loggin in and token generation.
$app->post("/token", "handleGetToken");
$app->post("/account", "handleCreateUserAccount");        

        
//Root
$app->get('/', "getResources"); 

//Data retrieval - HTTP GET methods for the exposed resources
$app->get('/anime', "getAllAnime");
$app->get('/anime/{anime_id:[0-9]+}', "getAnimeById");
// get all reviews associated with an anime
$app->get('/anime/{anime_id:[0-9]+}/reviews', "getAnimeReviews");


$app->get('/manga', "getAllManga");
// get all reviews associated with a manga
$app->get('/manga/{manga_id:[0-9]+}/reviews', "getMangaReviews");

$app->get('/users', "getAllUsers");
$app->get('/users/{user_id:[0-9]+}/reviews', "getUserReviews");
$app->get('/users/{user_id:[0-9]+}/toWatch/anime', "getUserAnimeToWatch");
$app->get('/users/{user_id:[0-9]+}/watched/anime', "getUserAnimeWatched");
$app->get('/users/{user_id:[0-9]+}/toWatch/manga', "getUserMangaToWatch");
$app->get('/users/{user_id:[0-9]+}/watched/manga', "getUserMangaWatched");

$app->get('/studios', "getAllStudios");
// get all anime associated with a studio
$app->get('/studios/{studio_id:[0-9]+}/anime', "getAnimeByStudio");

$app->get('/reviews', "getAllReviews");
$app->get('/reviews/{review_id:[0-9]+}', "getReviewById");

$app->get('/genres', "getAllGenres"); //DONE -> WORKING
$app->get('/genres/{genre_id:[0-9]+}', "getGenreById"); //DONE -> WORKING
// get all anime associated with a genre
$app->get('/genres/{genre_id:[0-9]+}/anime', "getGenreAnime"); //Giu Build 1 -> DONE
// get all manga associated with a genre
$app->get('/genres/{genre_id:[0-9]+}/manga', "getGenreManga"); //Giu Build 1 -> DONE

//Data manipulation - HTTP POST, PUT, and DELETE methods for the exposed resources
$app->post('/anime', "createAnime");
$app->post('/manga', "createManga");
$app->post('/users', "createUsers");
$app->post('/reviews', "createReviews");

$app->put('/anime', "updateAnime");
$app->put('/manga', "updateManga");
$app->put('/users', "updateUsers");
$app->put('/reviews', "updateReviews");

//Delete
$app->delete('/users/{user_id:[0-9]+}', "deleteUsers");
$app->delete('/reviews/{review_id:[0-9]+}', "deleteReviews");

// Run the app.
$app->run();
