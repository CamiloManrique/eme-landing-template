<?php

if(!function_exists("route")){
    function route($path){
        $app_url = isset($_ENV['APP_URL']) ? $_ENV['APP_URL'] : "http://localhost";
        return $app_url."/".ltrim($path, "/");
    }
}