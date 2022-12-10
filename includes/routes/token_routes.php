<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . './../helpers/helper_functions.php';
require_once __DIR__ . './../helpers/response_codes.php';
require_once __DIR__ . './../models/BaseModel.php';
require_once __DIR__ . './../models/ApiUserModel.php';

function handleGetToken(Request $request, Response $response, array $args) {
    $user_data = $request->getParsedBody();
    $apiUser_Mode = new ApiUserModel();
    $jwtManager = new JWTManager();

    if (empty($user_data["email"]) || empty($user_data["password"])) {
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Email and password can not be null");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    $email = $user_data["email"];
    $password = $user_data["password"];

    $db_user = $apiUser_Mode->verifyEmail($email);
    if (!$db_user) {
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The provided email does not match our records.");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    // Now we verify if the provided passowrd.
    $db_user = $apiUser_Mode->verifyPassword($email, $password);
    if (!$db_user) {
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The provided password was invalid.");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

  
    $jwt_user_info = ["id" => $db_user["user_id"], "email" => $db_user["email"]];
    $expires_in = time() + 6000 * 10; // Change expires to 24 hours 
    $user_jwt = $jwtManager->generateToken($jwt_user_info, $expires_in);

    $response_data = json_encode([
        'status' => 1,
        'token' => $user_jwt,
        'message' => 'User logged in successfully!',
    ]);

    return response($response_data, HTTP_OK, $response);
}

// HTTP POST: URI /account 
// Creates a new user account.
function handleCreateUserAccount(Request $request, Response $response, array $args) {
    $user_data = $request->getParsedBody();
    $apiUser_model = new ApiUserModel();

    // check if data is not null,
    foreach($user_data as $property => $value){
        if(empty($value)){
            $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "$property property was not provided in the request");
            return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
        }  
    }

    // check if the email is in the correct format ()
    if(!filter_var($user_data["email"], FILTER_VALIDATE_EMAIL)){
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Invalid email format");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    $checkExistEmail = $apiUser_model->verifyEmail($user_data["email"]);
    if($checkExistEmail){
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "The specific email have already been used");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    $new_user = $apiUser_model->createUser($user_data);
     if (!$new_user) {
        $response_data = makeCustomJSONError(HTTP_METHOD_NOT_ALLOWED, "Failed to create the new user.");
        return response($response_data, HTTP_METHOD_NOT_ALLOWED, $response);
    }

    // The user account has been created successfully.
    return response(httpCreated(), HTTP_CREATED, $response);
}

