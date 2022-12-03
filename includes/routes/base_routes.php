<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;
use Symfony\Component\Yaml\Yaml;

/**
 * Function handle a get request for the root resource
 */
function getResources(Request $request, Response $response, $args) {
    $exposed_resources = array();
    $exposed_resources['GET | All Anime'] = '/anime';
    $exposed_resources['GET | All Studios'] = '/studios';
    $exposed_resources['GET | All Manga'] = '/manga';
    $exposed_resources['GET | All Users'] = '/users';
    $exposed_resources['GET | All Reviews'] = '/reviews';
    $exposed_resources['GET | All Genres'] = '/genres';
    $exposed_resources['GET | Anime By Id'] = '/anime/{anime_id}';
    $exposed_resources['GET | Manga By Id'] = '/manga/{manga_id}';
    $exposed_resources['GET | Reviews By Id'] = '/reviews/{review_id}';
    $exposed_resources['GET | Genre By Id'] = '/genre/{genre_id}';
    $exposed_resources['GET | Reviews By Anime'] = '/artists/{artist_id}/reviews';
    $exposed_resources['GET | Reviews By Manga'] = '/manga/{manga_id}/reviews';
    $exposed_resources['GET | Reviews By User'] = '/users/{user_id}';
    $exposed_resources['GET | Anime on User\'s \"To Watch\" list'] = '/users/{user_id}/toWatch/anime';
    $exposed_resources['GET | Anime on User\'s \"Watched\" list'] = '/users/{user_id}/watched/anime';
    $exposed_resources['GET | Manga on User\'s \"To Watch\" list'] = '/users/{user_id}/toWatch/manga';
    $exposed_resources['GET | Manga on User\'s \"Watched\" list'] = '/users/{user_id}/watched/manga';
    $exposed_resources['GET | Anime made by a given studio'] = '/studios/{studio_id}/anime';
    $exposed_resources['GET | Anime with a given genre'] = '/genres/{genre_id}/anime';
    $exposed_resources['GET | Manga with a given genre'] = '/genres/{genre_id}/manga';
    $exposed_resources['POST | Create Anime'] = '/anime';
    $exposed_resources['POST | Create Manga'] = '/manga';
    $exposed_resources['POST | Create Review'] = '/reviews';
    $exposed_resources['POST | Create User'] = '/users';
    $exposed_resources['PUT | Update Anime'] = '/anime';
    $exposed_resources['PUT | Update Manga'] = '/manga';
    $exposed_resources['PUT | Update Review'] = '/reviews';
    $exposed_resources['PUT | Update User'] = '/users';
    $exposed_resources['DELETE | Delete User By Id'] = '/users/{user_id}';
    $exposed_resources['DELETE | Delete Review By Id'] = '/reviews/{review_id}';
    $response->getBody()->write(json_encode($exposed_resources));
    return $response->withStatus(HTTP_OK);
}
