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
    $exposed_resources['GET | Artist By Id'] = '/artists/{artist_id}';
    $exposed_resources['GET | Album By Artist Id'] = '/artists/{artist_id}/albums';
    $exposed_resources['GET | Tracks By Artist And Album'] = '/artists/{artist_id}/albums/{album_id}/tracks';
    $exposed_resources['GET | Invoices By Customer Id'] = '/customers/{customer_id}/invoices';
    $exposed_resources['GET | All Customers'] = '/customers';
    $exposed_resources['POST | Create Artists'] = '/artists';
    $exposed_resources['PUT | Update Artists'] = '/artists';
    $exposed_resources['DELETE | Delete Artist By Id'] = '/artists/{artist_id}';
    $exposed_resources['DELETE | Delete Customer By Id'] = '/customers/{customer_id}';
    $response->getBody()->write(json_encode($exposed_resources));
    return $response->withStatus(HTTP_OK);
}
