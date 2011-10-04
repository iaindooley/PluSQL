<?php
    namespace Plusql;

    class Table
    {
        private $name;
        private $join_to;
        private $join_type;
        const INNER_JOIN = 'INNER JOIN';
        
        public function __construct($name)
        {
            $this->name    = $name;
            $this->join_to = array();
            $this->join_type = NULL;
        }
        
        public function name()
        {
            return $this->name;
        }

        public function joinType()
        {
            return $this->join_type;
        }

        public function setJoinType($type)
        {
            $this->join_type = $type;
        }

        public function joinTo()
        {
            return $this->join_to;
        }

        public function joinTable($table)
        {
            $this->join_to[$table->name()] = $table;
            $table->setJoinType(self::INNER_JOIN);
        }
    }
