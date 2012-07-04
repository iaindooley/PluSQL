<?php
    namespace PluSQL;
    use mysqli;
    
    class RawQuery
    {
        private $conn;

        public function __construct(Connection $conn)
        {
            $this->conn   = $conn;
        }
        
        public function run($sql)
        {
            return $this->conn->query($sql);
        }
    }
