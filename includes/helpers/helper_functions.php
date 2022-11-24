<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
//var_dump($_SERVER["REQUEST_METHOD"]);
use Slim\Factory\AppFactory;
use Symfony\Component\Yaml\Yaml;

require_once __DIR__ . './../helpers/response_codes.php';

/**
 * Verify requested resource representation.
 */
function checkRepresentation(Request $request, Response $response, $data) {
    $requested_format = $request->getHeader('Accept');
    if ($requested_format[0] === APP_MEDIA_TYPE_JSON) {
        $response_data = json_encode($data, JSON_INVALID_UTF8_SUBSTITUTE);
        $response_code = HTTP_OK;
    }

    else if ($requested_format[0] === APP_MEDIA_TYPE_XML) {
        $xml = new SimpleXMLElement('<xmlresponse/>');
        array2XML($xml, $data);
        $response_data = $xml->asXML();
        $response_code = HTTP_OK;
    }

    else if ($requested_format[0] === APP_MEDIA_TYPE_YAML) {
        $response_data = Yaml::dump($data);
        $response_code = HTTP_OK;
    }
    else {
        $response_data = httpUnsupportedMediaType();
        $response_code = HTTP_UNSUPPORTED_MEDIA_TYPE;
    }
    $response->getBody()->write($response_data);
    return $response->withStatus($response_code);
}

/**
 * Convert an array to XML.
 */
function array2XML($obj, $array)
{
    foreach ($array as $key => $value)
    {
        if(is_numeric($key))
            $key = 'item' . $key;

        if (is_array($value))
        {
            $node = $obj->addChild($key);
            array2XML($node, $value);
        }
        else
        {
            $obj->addChild($key, htmlspecialchars($value));
        }
    }
}

/**
 * Function to handle error 404 (Not Found)
 */
function checkData($data, Response $response, Request $request) {
    if (!$data) {
        $response_data = httpNotFound();
        $response->getBody()->write($response_data);
        return $response->withStatus(HTTP_NOT_FOUND);
    }
    else {
        return checkRepresentation($request, $response, $data);
    }
}