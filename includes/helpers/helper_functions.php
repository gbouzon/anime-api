<?php

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
    //to be fixed
    else if ($requested_format[0] === APP_MEDIA_TYPE_XML) {
        $xml = new SimpleXMLElement('');
        array_walk_recursive($data, array ($xml,'addChild'));
        $response_data = $xml->asXML();
        $response_code = HTTP_OK;
    }
    //to be fixed
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