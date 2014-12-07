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
        public static $rootURI = ''; // TODO: Support multiple directories.
        
        /**
         * The directory to store compiled views.
         * 
         * @var string
         */
        public static $storageDir = '';
        
        /**
         * The uncompiled view.
         * 
         * @var string
         */
        protected $_file = '';
        
        /**
         * The compiled view.
         * 
         * @var string
         */
        protected $_compiledFile = '';
        
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
         * The extension of the views.
         */
        const STORAGE_DIR_PERMISSIONS = 0777;
        
        /**
         * Initializes the class.
         * 
         * @param  string $view The URI of the view.
         * @param  array  $data The data to pass to the view.
         */
        public function __construct($view, array $data = array()){
            self::$rootURI = rtrim(self::$rootURI, '/').'/';
            self::$storageDir = (empty(self::$storageDir)) ? 'storage/views' : self::$storageDir; 
            self::$storageDir = rtrim(self::$storageDir, '/').'/';
            
            $this->_file = self::$rootURI.$view.self::VIEW_EXTENSION;
            $this->_data = $data;
            
            if(!self::exists($this->_file)){
                throw new ViewException('View '.$this->_file.' is not readable. Does it exist?');
            }
            
            ob_start();
            require_once($this->_file);
            $this->_content = ob_get_contents();
            ob_end_clean();
            
            $this->_compiledFile = self::$storageDir.md5($this->_file);
            
            if($this->_isExpired()){
                if(!file_exists(self::$storageDir)){
                    mkdir(self::$storageDir, self::STORAGE_DIR_PERMISSIONS, true);
                }
                
                $compiledContent = (new Compiler($this->_content))->getCompiledContent();
                
                if(is_writable(self::$storageDir)){
                    if($fileHandle = fopen($this->_compiledFile, 'w')){
                        //chmod($file, self::VIEW_PERMISSIONS);
                        fwrite($fileHandle, $compiledContent);
                        fclose($fileHandle);
                    }
                } else {
                    throw new ViewException('Cannot create/write to '.self::$storageDir.'. Permission denied.');
                }
            } else {
                $this->_compiledFile = self::$storageDir.md5($this->_file);
            }
        }
        
        /**
         * Displays the view.
         */
        // public function __destruct(){
           // extract($this->_data);
           // require_once($this->_compiledFile);
        // }
        
        /**
         * Instantiates the class.
         * 
         * @param  string $view The URI of the view.
         * @param  array  $data The data to pass to the view.
         * 
         * @return Tres\templating\View To make method chaining available.
         */
        public static function make($view, array $data = array()){
            $static = new static($view, $data);
            
            extract($static->_data);
            require_once($static->_compiledFile);
        }
        
        /**
         * Tells whether a file exists and is readable or not.
         * 
         * @param  string $file The file.
         * @return bool
         */
        public static function exists($file){
            return is_readable($file);
        }
        
        /**
         * Tells whether the compiled view is expired or not.
         * 
         * @return bool
         */
        protected function _isExpired(){
            if(is_readable($this->_compiledFile)){
                return filemtime($this->_file) >= filemtime($this->_compiledFile);
            }
            
            return true;
        }
        
    }
    
}
