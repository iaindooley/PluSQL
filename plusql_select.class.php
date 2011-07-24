<?php
    class PlusqlSelect
    {
        private $tables;
        private $target;
        private $initial_table;
        
        public function __construct()
        {
            $this->tables = array();
            $this->target = NULL;
            $this->initial_table = NULL;
        }
        
        public function __get($name)
        {
            $previous = NULL;

            if($this->target !== NULL)
                $previous = $this->target;

            if(!isset($this->tables[$name]))
            {
                $this->tables[$name] = new PlusqlTable($name);
                
                if($previous)
                    $previous->joinTable($this->tables[$name]);
            }
            
            $this->target = $this->tables[$name];
            

            if($this->initial_table === NULL)
                $this->initial_table = $this->target;

            return $this;
        }
        
        public function _()
        {
            $clause = '';
            $stack = array($this->initial_table);
            
            while($t = array_pop($stack))
            {
                if(!$clause)
                    $clause = 'from '.$t->name().' ';
                
                foreach($t->joinTo() as $t)
                {
                    $clause .= $t->joinType().' '.$t->name().' ON ';
                    //TODO: ON clause - automatically detect many-to-many joins
                    $stack[] = $t;
                }
            }
            
            die($clause);
        }
        
        // default inner join, allow left call to override
        // allow custom on clause
        // should automatically detect mapping tables
        // let's create a way to retrieve a table previously added
    }
