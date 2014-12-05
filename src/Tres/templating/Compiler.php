<?php

namespace Tres\templating {
    
    /*
    |--------------------------------------------------------------------------
    | Compiler
    |--------------------------------------------------------------------------
    | 
    | Compiles the views using the syntax of the Tres templating engine.
    | 
    */
    class Compiler {
        
        /**
         * The uncompiled content.
         * 
         * @var string
         */
        protected $_content = '';
        
        /**
         * The compiled content.
         * 
         * @var string
         */
        protected $_compiledContent = '';
        
        /**
         * Array of opening and closing tags for raw echos.
         * 
         * @var array
         */
        protected $_rawTags = ['{{!', '}}'];
        
        /**
         * Array of opening and closing tags for HTML escaped echos.
         * 
         * @var array
         */
        protected $_escapeTags = ['{{', '}}'];
        
        /**
         * Array of opening and closing tags for multi-block comments.
         * 
         * @var array
         */
        protected $_commentTags = ['{{--', '--}}'];
        
        /**
         * Initializes the compiler.
         * 
         * @param string $content The uncompiled content.
         */
        public function __construct($content){
            $this->_content = $content;
            
            $result = '';
            
            foreach(token_get_all($this->_content) as $token){
                $result .= (is_array($token)) ? $this->_parseToken($token) : $token;
            }
            
            $this->_compiledContent = $result;
        }
        
        /**
         * Returns the compiled content.
         * 
         * @return string
         */
        public function getCompiledContent(){
            return $this->_compiledContent;
        }
        
        /**
         * Parses the token.
         * 
         * @param  string $token The token.
         * @return string The parsed token.
         */
        protected function _parseToken($token){
            list($id, $content) = $token;
            
            if($id == T_INLINE_HTML){
                $content = $this->_compileComments($content);
                $content = $this->_compileStatements($content);
                $content = $this->_compileEchoes($content);
            }
            
            return $content;
        }
        
        /**
         * Compiles the comments into valid PHP.
         * 
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        protected function _compileComments($content){
            $pattern = sprintf('/%s((.|\s)*?)%s/', $this->_commentTags[0], $this->_commentTags[1]);
            
            return preg_replace($pattern, '<?php /*$1*/ ?>', $content);
        }
        
        protected function _compileStatements($content){
            return $content;
        }
        
        protected function _compileEchoes($content){
            return $content;
        }
        
    }
    
}
