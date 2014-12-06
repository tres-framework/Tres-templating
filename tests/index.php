<?php

//use Tres\templating\Config as TemplatingConfig;
use Tres\templating\View;
use Tres\templating\ViewException;

spl_autoload_register(function($class){
    $dirs = [
        dirname(__DIR__).'/src/',
        dirname(__DIR__).'/tests/',
    ];
    
    foreach($dirs as $dir){
        $file = str_replace('\\', '/', $dir.$class.'.php');
        
        if(is_readable($file)){
            require_once($file);
        }
    }
});

if(is_readable(dirname(__DIR__).'/tests/Whoops/Run.php')){
    $whoops = new Whoops\Run;
    $whoops->pushHandler(new Whoops\Handler\PrettyPageHandler);
    $whoops->register();
}

function e($str, $flags = ENT_QUOTES, $encoding = 'UTF-8'){
    return htmlspecialchars($str, $flags, $encoding);
}

View::$rootURI = __DIR__.'/views/';

$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

switch($page){
    case 'home':
        $data = [
            'html' => '<b>Some</b> <i>HTML</i> <a href="#">here</a>.',
            'y' => '<script>alert("XSS protection.");</script>',
        ];
        View::make('home', $data);
    break;
    
    default:
        View::make('error-404');
    break;
}
