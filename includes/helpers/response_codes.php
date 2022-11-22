<?php

/**
 * Function to handle response code 200 
 */
function httpOK() {
    $response_data = array(
        "status code:" => HTTP_OK,
        "message:" => "OK"
    );
    return json_encode($response_data);
}

/**
 * Function to handle response code 201 
 */
function httpCreated() {
    $response_data = array(
        "status code:" => HTTP_CREATED,
        "message:" => "The request has been fulfilled and has resulted in one or more new resources being created."
    );
    return json_encode($response_data);
}

/**
 * Function to handle response code 204
 */
function httpNoContent() {
    $response_data = array(
        "status code:" => HTTP_NO_CONTENT,
        "message:" => "OK. No content to return."
    );
    return json_encode($response_data);
}

/**
 * Function to handle error 405 (Unsupported Operation)
 */
function httpMethodNotAllowed() {
    $response_data = array(
        "status code:" => HTTP_METHOD_NOT_ALLOWED,
        "message:" => "The method specified in the Request-Line is not allowed for the resource identified by the Request-URI."
    );
    return json_encode($response_data);
}

/**
 * Function to handle error 415 (Unsupported Media Type)
 */
function httpUnsupportedMediaType() {
    $response_data = array(
        "status code:" => HTTP_UNSUPPORTED_MEDIA_TYPE,
        "message:" => "Cannot process the request because the media type is not supported. Only JSON, XML and YAML are supported."
    );
    return json_encode($response_data);
}

/**
 * Function to handle error 404 (Not Found)
 */
function httpNotFound() {
    $response_data = array(
        "status code:" => HTTP_NOT_FOUND,
        "message:" => "Requested resource not found"
    );
    return json_encode($response_data);
}

/**
 * Function to handle error 418
 */
function httpTeapot() {
    $response_data = array(
        "status code:" => HTTP_TEAPOT,
        "message:" => "I'm a teapot"
    );
    return json_encode($response_data);
}


/**
 * Returns a custom error using the passed error code and message
 */
function makeCustomJSONError($error_code, $error_message) {
    $error_data = array(
        "status code:" => $error_code,
        "message:" => $error_message
    );
    return json_encode($error_data);
}

