<?php
    namespace PluSQL;
    use Iterator;

    class QueryIterator implements Iterator
    {
        private $index;
        private $starting_index;
        private $query;
        private $table;
        private $current_row;
        private $constrain;
        private $current_key_signature;
        
        public function __construct(Query $query,$table,$index = 0)
        {
            $this->index = $index;
            $this->starting_index = $index;
            $this->query = $query;
            $this->table = $table;
            $this->current_row = NULL;
            $this->constrain = array();
            $test = new QueryRow($this->query,$this->table,$this->index);
            $test->keySignature();
        }
        
        public function constrainKeys($pairs)
        {
            $this->constrain = $pairs;
        }
        
        public function checkConstraints()
        {
            $row = $this->query->rowAtIndex($this->index);
            
            foreach($this->constrain as $name => $value)
            {
                if(!isset($row[$name]))
                    throw new InvalidQueryRowException('I am somehow constrained to field: '.$name.' but that doesn\'t exist in the query ... que?');
                if($row[$name] != $value)
                    throw new InvalidQueryRowException('Constrain no que es pendeho mucho');
            }
        }
        
        public function current()
        {
            try
            {
                $this->checkConstraints();
                $this->current_row = new QueryRow($this->query,$this->table,$this->index);
                $ret = $this->current_row;
            }
            
            catch(InvalidQueryRowException $exc)
            {
                $ret = FALSE;
            }
            
            return $ret;
        }
        
        public function key()
        {
            return $this->index;
        }
        
        public function next()
        {
            try
            {
                $next_row = new QueryRow($this->query,$this->table,$this->index);

                while($next_row->keySignature() == $this->current_row->keySignature())
                {
                    $this->index++;
                    $next_row = new QueryRow($this->query,$this->table,$this->index);
                    $this->checkConstraints();
                }
                
                $this->current_row = $next_row;
                $ret = $next_row;
            }
            
            catch(InvalidQueryRowException $exc)
            {
                $ret = FALSE;
            }
            
            return $ret;
        }
        
        public function rewind()
        {
            $this->index = $this->starting_index;
            $this->current_row = NULL;
        }
        
        public function valid()
        {
            try
            {
                $ret = is_object(new QueryRow($this->query,$this->table,$this->index));
                $this->checkConstraints();
            }
                
            catch(InvalidQueryRowException $exc)
            {
                $ret = FALSE;
            }
            
            return $ret;
        }
        
        public function __get($name)
        {
            return $this->current()->$name;
        }
    }
