<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';

//constants
require_once './includes/app_constants.php';
require_once './includes/helpers/helper_functions.php';

//routes
require_once './includes/routes/base_routes.php';
require_once './includes/routes/artists_routes.php';
require_once './includes/routes/customers_routes.php';
require_once './includes/routes/album_routes.php';

$app = AppFactory::create();
$app->addRoutingMiddleware();
$app->addBodyParsingMiddleware();
$errorMiddleware = $app->addErrorMiddleware(true, true, true);
$app->setBasePath("/webservices/music-api");

//Root
$app->get('/', "getResources"); 

//Data retrieval - HTTP GET methods for the exposed resources
$app->get('/artists', "getAllArtists");
$app->get('/artists/{artist_id:[0-9]+}', "getArtistById"); 
$app->get('/artists/{artist_id:[0-9]+}/albums', "getAlbumsByArtistId"); 
$app->get('/artists/{artist_id:[0-9]+}/albums/{album_id:[0-9]+}/tracks', "getTracksByArtistAlbum"); 
$app->get('/customers/{customer_id:[0-9]+}/invoices', "getPurchasesByCustomerId");
$app->get('/invoices', "getPurchasesByCustomerId");
$app->get('/customers', "getAllCustomers"); 

//Data manipulation - HTTP POST, PUT, and DELETE methods for the exposed resources
$app->post('/artists', "createArtists"); 
$app->put('/artists', "updateArtists"); 
$app->delete('/artists/{artist_id:[0-9]+}', "deleteArtist"); 
$app->delete('/customers/{customer_id:[0-9]+}', "deleteCustomer"); 

// Run the app.
$app->run();
