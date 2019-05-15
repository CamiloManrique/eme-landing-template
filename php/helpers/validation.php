<?php

use Slim\Http\Response;
use Illuminate\Contracts\Validation\Validator;

if(!function_exists("return_validation_errors")){
    function return_validation_errors(Response $response, Validator $validator){
        return $response->withJson(['errors' => $validator->errors()], 422);
    }
}