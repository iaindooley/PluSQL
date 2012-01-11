<?php
    class Plusql
    {
        private static $instance = NULL;
        private $connections;
        const HOST = 0;
        const USER = 1;
        const PASS = 2;
        const NAME = 3;
        private $credentials;

        private function __construct()
        {
            $this->connections = array();
            $this->credentials = array();
        }
        
        public static function credentials($name,$details)
        {
            if(count($details) != 4)
                throw new InvalidCredentialsException('You need to pass in a 4 element array of credentials: host, username, password and database name to Plusql::credentials()');
            
            if(self::$instance === NULL)
                self::$instance = new Plusql();
            
            self::$instance->credentials[$name] = $details;
        }

        public static function connect($credentials)
        {
            if(self::$instance === NULL)
                self::$instance = new Plusql();
            
            if(!isset(self::$instance->credentials[$credentials]))
                throw new InvalidCredentialsException('Unable to begin Plusql session with credentials named: '.$credentials.' (cos they don\'t exist)');

            $host = self::$instance->credentials[$credentials][0];
            $user = self::$instance->credentials[$credentials][1];
            $password = self::$instance->credentials[$credentials][2];
            $dbname = self::$instance->credentials[$credentials][3];
            $key = implode('-',self::$instance->credentials[$credentials]);
            
            if(!isset(self::$instance->connections[$key]))
            {
                $conn = new plusql\Connection($host,$user,$password,$dbname);
                self::$instance->connections[$key] = $conn;
            }
            else
                $conn = self::$instance->connections[$key];
            
            $conn->connect();
            return $conn;
        }
        
        public function from($credentials)
        {
            return new plusql\Select(self::connect($credentials));
        }

        public function into($credentials)
        {
            return new plusql\Insert(self::connect($credentials));
        }

        public function against($credentials)
        {
            return new plusql\RawQuery(self::connect($credentials));
        }
    }

    class PlusqlConnectionException extends Exception {}
    class EmptySetException extends Exception {}
    class InvalidCredentialsException extends Exception{}
