<?php
    namespace PluSQL;
    
    class SqlFunction
    {
        private $str;
        
        public function __construct($str)
        {
            $this->str = $str;
        }
        
        public function __toString()
        {
            return $this->str;
        }
    }
