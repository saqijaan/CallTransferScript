<?php


function env($field,$default=null){
    global $AGENT_NUMBER,$CLIENT_SID,$AUTH_TOKEN;
    if (isset( $_ENV [$field])){
        return $_ENV [$field];
    }
    else
        return $default;
}
function url($url){
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http")."://".$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF'])."/".$url;
}