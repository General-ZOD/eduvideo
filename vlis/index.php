<?php
//values for client-side purposes
define('DOMAIN', 'http://pazzionate.com/');
define('DOMAIN', 'http://localhost:83/');
define('IMAGE', DOMAIN . 'img/');
define('THUMBNAILS', IMAGE . 'thb/');
define('CSS', DOMAIN . 'css/');
define('JS', DOMAIN . 'js/');
define('VID', DOMAIN . 'vid/');
$root = dirname(__FILE__); //root of the application
define('ROOT', $root . '/');
define('IMG_SRC', ROOT . 'img/');
define('THB_NAIL', IMG_SRC . 'thb/');
define('VID_SRC', ROOT . 'vid/');

/*
 *values for server-side purposes
 *server-side scripts are not in the document root path; this reduces the possibility of browser-based attacks
*/
date_default_timezone_set('America/Chicago');

define('SCRIPT', dirname($root) . '/src/');
define('CONFIG', SCRIPT . 'config/');
define('DOC', SCRIPT . 'doc/');
define('LIB', SCRIPT . 'lib/');
define('LOG', SCRIPT . 'log/');
define('TEMPLATES', SCRIPT . 'templates/');
define('MAIL_TEMPLATE', TEMPLATES . 'mail/');
define('DB', LIB . 'db/');
define('ORM', DB . 'orm/');
define('MODULES', LIB . 'modules/');
define('ADMIN_MODULES', MODULES . 'admin/');
define('ADMIN_CATEGORIES', ADMIN_MODULES . 'categories/');
define('ADMIN_QUESTIONS', ADMIN_MODULES . 'questions/');
define('ADMIN_USERS', ADMIN_MODULES . 'users/');
define('ADMIN_VIDEOS', ADMIN_MODULES . 'videos/');
define('AUTHENTICATION_MOD', MODULES . 'authentication/');
define('AUTHORIZATION_MOD', MODULES . 'authorization/');
define('CATEGORIES_MOD', MODULES . 'categories/');
define('HOME_MOD', MODULES . 'home/');
define('PROFILE_MOD', MODULES . 'profile/');
define('REGISTRATION_MOD', MODULES . 'signup/');
define('WATCH_VIDEO_MOD', MODULES . 'video_watch/');
define('UTILS', LIB . 'utils/');
define('INTERFACES', UTILS . 'interfaces/');
define('VIDEO_UPLOAD_MOD', MODULES . 'video_upload/');
define('VIDEO_SEARCH_MOD', MODULES . 'search/');
define('TMP_VIDEO', LIB . 'tmp_video/');



setReporting(true); ////ALWAYS SET THIS TO 'FALSE' BEFORE DEPLOYING
/** AutoLoad class files as soon as needed ***/
spl_autoload_register('loadClass');

include_once CONFIG . 'settings.php';
$registry = new Registry();
Factory::getRegistry($registry);
Factory::getParameters($parameters); //parameter is defined in the settings file

// URI will always be of the form /specific_path
$request = $_SERVER["REQUEST_URI"];
$path = explode("/", $request);

$controller_obj=null;
switch($path[1]){
    case '': $controller_obj = new HomeController($path, $registry); $controller_obj->process($_POST); break;
    case 'admin': $controller_obj = new AdminController($path, $registry); $controller_obj->process($_POST); break;    
    case 'categories': $controller_obj = new CategoriesController($path, $registry); $controller_obj->process($_POST); break;
    case 'contact': break;
    case 'profile': $controller_obj = new ProfileController($path, $registry); $controller_obj->process($_POST); break;    
    case 'login': $controller_obj = new LoginController($path, $registry); $controller_obj->process($_POST); break;
    case 'logout': $controller_obj = new LogoutController($path, $registry); $controller_obj->process(); break;                  
    case 'signup': $controller_obj = new SignupController($path, $registry); $controller_obj->process($_POST); break;
    case 'search': $controller_obj = new VideoSearchController($path, $registry); $controller_obj->process($_GET); break;    
    case 'tos': break;
    case 'upload': $controller_obj = new VideoUploadController($path, $registry); $controller_obj->process($_POST); break;
    case 'watch' : $controller_obj = new WatchVideoController($path, $registry); $controller_obj->process($_POST); break;   
}



function setReporting($is_dev_environ){
    if ($is_dev_environ === true){
        error_reporting(E_ALL);
        ini_set('display_errors', 'On');
        ini_set('log_errors', 'On');
        ini_set('error_log', SCRIPT . 'error.log');
    }else{
        error_reporting(E_ALL);
        ini_set('display_errors', 'Off');
        ini_set('log_errors', 'On');
        ini_set('error_log', SCRIPT . 'error.log');
    }
}

function loadClass($class){
    $class = strtolower($class);
    if (file_exists(DB . $class. '.php'))
        include_once DB . $class . '.php';
    else if (file_exists(ORM . $class. '.php'))
        include_once ORM . $class . '.php';
    else if (file_exists(ADMIN_MODULES . $class . '.php'))
        include_once ADMIN_MODULES . $class . '.php';
    else if (file_exists(ADMIN_CATEGORIES . $class . '.php'))
        include_once ADMIN_CATEGORIES . $class . '.php';
    else if (file_exists(ADMIN_QUESTIONS . $class . '.php'))
        include_once ADMIN_QUESTIONS . $class . '.php';
    else if (file_exists(ADMIN_USERS . $class . '.php'))
        include_once ADMIN_USERS . $class . '.php';         
    else if (file_exists(ADMIN_VIDEOS . $class . '.php'))
        include_once ADMIN_VIDEOS . $class . '.php';         
    else if (file_exists(AUTHENTICATION_MOD . $class . '.php'))
        include_once AUTHENTICATION_MOD . $class . '.php';
    else if (file_exists(AUTHORIZATION_MOD . $class . '.php'))
        include_once AUTHORIZATION_MOD . $class . '.php';
    else if (file_exists(CATEGORIES_MOD. $class . '.php'))
        include_once CATEGORIES_MOD . $class . '.php';        
    else if (file_exists(HOME_MOD . $class . '.php'))
        include_once HOME_MOD . $class. '.php';
    else if (file_exists(PROFILE_MOD . $class . '.php'))
        include_once PROFILE_MOD . $class . '.php';        
    else if (file_exists(REGISTRATION_MOD . $class . '.php'))
        include_once REGISTRATION_MOD . $class. '.php';
    else if (file_exists(VIDEO_SEARCH_MOD . $class . '.php'))
        include_once VIDEO_SEARCH_MOD . $class. '.php';        
    else if (file_exists(WATCH_VIDEO_MOD . $class . '.php'))
        include_once WATCH_VIDEO_MOD . $class. '.php';        
    else if (file_exists(INTERFACES . $class . '.php'))
        include_once INTERFACES . $class . '.php';
    else if (file_exists(UTILS . $class . '.php'))
        include_once UTILS . $class. '.php';
    else if (file_exists(VIDEO_UPLOAD_MOD. $class . '.php'))
        include_once VIDEO_UPLOAD_MOD . $class . '.php';         
}