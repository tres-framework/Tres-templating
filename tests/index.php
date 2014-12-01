<?php

//use Tres\templating\Config as TemplatingConfig;
use Tres\templating\View;

require_once('../src/Tres/templating/View.php');

View::$rootURI = __DIR__.'/views/';

$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

switch($page){
    case 'home':
        View::make('home');
    break;
    
    default:
        View::make('error-404');
    break;
}
