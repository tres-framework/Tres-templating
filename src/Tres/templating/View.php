<?php

namespace Tres\templating {
    
    use Exception;
    
    class ViewException extends Exception {}
    
    class View {
        
        /**
         * The root URI where the views are stored.
         * 
         * @var string
         */
        public static $rootURI = '';
        
        /**
         * The URI to the view.
         * 
         * @var string
         */
        protected $_view = '';
        
        /**
         * The data to pass to the view.
         * 
         * @var array
         */
        protected $_data = [];
        
        /**
         * The content of the view.
         * 
         * @var string
         */
        protected $_content = '';
        
        /**
         * The extension of the views.
         */
        const VIEW_EXTENSION = '.php';
        
        /**
         * Initializes the class.
         * 
         * @param  string $view The URI of the view.
         * @param  array  $data The data to pass to the view.
         * 
         * @return $this  To make method chaining available.
         */
        public function __construct($view, array $data = []){
            $this->_view = $view;
            $this->_data = $data;
            
            self::$rootURI = rtrim(self::$rootURI, '/').'/';
            
            if(!self::exists($this->_view)){
                throw new ViewException('View '.$this->_view.' is not readable. Does it exist?');
            }
            
            ob_start();
            require_once(self::$rootURI.$this->_view.self::VIEW_EXTENSION);
            $this->_content = ob_get_contents();
            ob_end_clean();
        }
        
        /**
         * Displays the view.
         */
        public function __destruct(){
            echo $this->_content;
        }
        
        /**
         * Instantiates the class.
         * 
         * @param  string $view The URI of the view.
         * @param  array  $data The data to pass to the view.
         * 
         * @return Tres\templating\View To make method chaining available.
         */
        public static function make($view, array $data = []){
            return new static($view, $data);
        }
        
        /**
         * Tells whether a view exists or not.
         * 
         * @param  string $view The URI to the view.
         * @return bool
         */
        public static function exists($view){
            return is_readable(self::$rootURI.$view.self::VIEW_EXTENSION);
        }
        
    }
    
}
