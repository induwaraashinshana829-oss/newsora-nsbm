<?php

$path=rawurldecode(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH)?:'/');
if(preg_match('~(?:^|/)(?:database|includes)(?:/|$)|(?:^|/)\.[^/]+~i',$path)||preg_match('~^/uploads/.*\.(?:php[0-9]?|phtml|phar|cgi|pl|sh)$~i',$path)){http_response_code(403);exit('Forbidden');}
return false;

