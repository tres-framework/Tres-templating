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
        
        /**
         * Compiles statements into valid PHP.
         *  
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        protected function _compileStatements($content){
            $callback = function($match){
                if(method_exists($this, $method = 'compile'.ucfirst($match[1]))){
                    $match[0] = $this->$method(array_get($match, 3));
                }
                
                return isset($match[3]) ? $match[0] : $match[0].$match[2];
            };
            
            return preg_replace_callback('/\B@(\w+)([ \t]*)(\( ( (?>[^()]+) | (?3) )* \))?/x', $callback, $content);
        }
        
        /**
         * Compiles the different kinds of echoes.
         * 
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        protected function _compileEchoes($content){
            $content = $this->_compileRawEchoes($content);
            $content = $this->_compileEscapedEchoes($content);
            
            return $content;
        }
        
        /**
         * Compiles raw echoes.
         * 
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        protected function _compileRawEchoes($content){
            $pattern = sprintf('/(@)?%s\s*(.+?)\s*%s(\r?\n)?/s', $this->_rawTags[0], $this->_rawTags[1]);
            $callback = function($matches){
                $whitespace = empty($matches[3]) ? '' : $matches[3].$matches[3];
                return $matches[1] ?
                       substr($matches[0], 1) :
                       '<?= '.$this->_compileDefaultEchoes($matches[2]).'; ?>'.$whitespace;
            };
            
            return preg_replace_callback($pattern, $callback, $content);
        }
        
        /**
         * Compiles escaped echoes.
         * 
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        protected function _compileEscapedEchoes($content){
            $pattern = sprintf('/%s\s*(.+?)\s*%s(\r?\n)?/s', $this->_escapeTags[0], $this->_escapeTags[1]);
            $callback = function($matches){
                $whitespace = empty($matches[2]) ? '' : $matches[2].$matches[2];
                return '<?= e('.$this->_compileDefaultEchoes($matches[1]).'); ?>'.$whitespace;
            };
            
            return preg_replace_callback($pattern, $callback, $content);
        }
        
        /**
         * Compiles the default values for the echo statements.
         *
         * @param  string $content The uncompiled content.
         * @return string The compiled content.
         */
        public function _compileDefaultEchoes($content){
            return preg_replace('/^(?=\$)(.+?)(?:\s+or\s+)(.+?)$/s', 'isset($1) ? $1 : $2', $content);
        }
        
        /**
         * Compiles the if statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileIf($expression){
            return "<?php if{$expression}: ?>";
        }
        
        /**
         * Compiles the else-if statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileElseif($expression){
            return "<?php elseif{$expression}: ?>";
        }
        
        /**
         * Compiles the else statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileElse($expression){
            return "<?php else: ?>";
        }
        
        /**
         * Compiles the end-if statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileEndif($expression){
            return '<?php endif; ?>';
        }
        
        /**
         * Compiles the unless statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileUnless($expression){
            return "<?php if(!{$expression}): ?>";
        }
        
        /**
         * Compiles the end-unless statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileEndunless($expression){
            return '<?php endif; ?>';
        }
        
        /**
         * Compiles the for statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileFor($expression){
            return "<?php for{$expression}: ?>";
        }
        
        /**
         * Compile the end-for statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileEndfor($expression){
            return '<?php endfor; ?>';
        }
        
        /**
         * Compiles the while statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileWhile($expression){
            return "<?php while{$expression}: ?>";
        }
        
        /**
         * Compile the end-while statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileEndwhile($expression){
            return '<?php endwhile; ?>';
        }
        
        /**
         * Compiles the foreach statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileForeach($expression){
            return "<?php foreach{$expression}: ?>";
        }
        
        /**
         * Compiles the end-if statements into valid PHP.
         *
         * @param  string $expression
         * @return string
         */
        protected function compileEndforeach($expression){
            return '<?php endforeach; ?>';
        }
        
    }
    
}
