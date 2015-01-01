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

function array_get($array, $key, $default = null){
    if(is_null($key)){
        return $array;
    }
    
    if(isset($array[$key])){
        return $array[$key];
    }
    
    foreach(explode('.', $key) as $segment){
        if(!is_array($array) || ! array_key_exists($segment, $array)){
            return $default;
        }
        
        $array = $array[$segment];
    }
    
    return $array;
}

View::$rootURI = __DIR__.'/views/';

$page = (isset($_GET['page'])) ? $_GET['page'] : 'home';

switch($page){
    case 'home':
        $data = [
            'html' => '<b>Some</b> <i>HTML</i> <a href="#">here</a>.',
            'array' => [
                'first key' => 'has a value',
                'second key' => 'has also a value',
                '3rd key' => 'as well',
                '4th key',
            ],
            'x' => 17,
            'y' => '<script>alert("XSS protection.");</script>',
        ];
        View::make('home', $data);
    break;
    
    default:
        View::make('error-404');
    break;
}
