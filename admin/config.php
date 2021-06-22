<?php
// HTTP
define('HTTP_SERVER', 'https://'.$_SERVER['HTTP_HOST'].'/admin/');
define('HTTP_CATALOG', 'https://'.$_SERVER['HTTP_HOST'].'/');

// HTTPS
define('HTTPS_SERVER', 'https://'.$_SERVER['HTTP_HOST'].'/admin/');
define('HTTPS_CATALOG', 'https://'.$_SERVER['HTTP_HOST'].'/');

define('LOCAL_PATH', '/home/g/PhpstormProjects/medfashion/');
//define('SERVER_PATH', '/home/c/cl57903/medfashion/');
define('SERVER_PATH', '/home/a0013053/domains/eg-med.ru/');

$host = $_SERVER['HTTP_HOST'];

if($host == 'localhost:8000'){
    $path_dir = LOCAL_PATH;
}else{
    $path_dir = SERVER_PATH;
}
define('PATH', $path_dir);

// DIR
define('DIR_APPLICATION', PATH.'public_html/admin/');
define('DIR_SYSTEM', PATH.'public_html/system/');
define('DIR_IMAGE', PATH.'public_html/image/');
define('DIR_STORAGE', PATH.'storage/');
define('DIR_CATALOG', PATH.'public_html/catalog/');
define('DIR_LANGUAGE', DIR_APPLICATION . 'language/');
define('DIR_TEMPLATE', DIR_APPLICATION . 'view/template/');
define('DIR_CONFIG', DIR_SYSTEM . 'config/');
define('DIR_CACHE', DIR_STORAGE . 'cache/');
define('DIR_DOWNLOAD', DIR_STORAGE . 'download/');
define('DIR_LOGS', DIR_STORAGE . 'logs/');
define('DIR_MODIFICATION', DIR_STORAGE . 'modification/');
define('DIR_SESSION', DIR_STORAGE . 'session/');
define('DIR_UPLOAD', DIR_STORAGE . 'upload/');

// DB
define('DB_DRIVER', 'mysqli');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'a0013053_medf');
define('DB_PASSWORD', 'ZqaXwsCed123456');
define('DB_DATABASE', 'a0013053_medf');
define('DB_PORT', '3306');
define('DB_PREFIX', 'oc_');

// OpenCart API
define('OPENCART_SERVER', 'https://www.opencart.com/');
