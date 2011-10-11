<?php
    namespace plusql;
    
    class Insert
    {
        private $conn;

        public function __construct(Connection $conn)
        {
            $this->conn = $conn;
        }
    }
