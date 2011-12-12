<?php
    namespace plusql;
    use Exception;
    
    class Insert
    {
        private $conn;

        public function __construct(Connection $conn)
        {
            $this->conn = $conn;
        }
        
        public function __call($name,$args)
        {
            echo $name.PHP_EOL;
            
            if(!is_array($args) || (count($args) > 1))
                throw new InvalidInsertArgumentsException('When you call a method on Insert you should pass in an array of key/value pairs to be inserted');
        }
    }

    class InvalidInsertArgumentsException extends Exception{}
