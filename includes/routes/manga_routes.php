<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/MangaModel.php';

