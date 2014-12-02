<?php

//use Tres\templating\Config as TemplatingConfig;
use Tres\templating\View;
use Tres\templating\ViewException;

spl_autoload_register(function($class){
    $file = dirname(__DIR__).'/src/'.str_replace('\\', '/', $class.'.php');
    
    require_once($file);
});

View::$rootURI = __DIR__.'/views/';

$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

var_dump(View::exists('home'));
var_dump(View::exists('error-404'));

switch($page){
    case 'home':
        View::make('home');
    break;
    
    default:
        View::make('error-404');
    break;
}
