<?php
    namespace PluSQL;
    use Exception;

    class QueryRow
    {
        private $query;
        private $table;
        private $data;
        private $index;

        public function __construct($query,$table,$index)
        {
            $this->query = $query;
            $this->index = $index;
            $this->table_inspector = TableInspector::forTable($table,$this->query->link());
            $this->data = $this->query->rowAtIndex($this->index);

            if(!is_array($this->data))
                throw new InvalidQueryRowException('You have tried to create a QueryRow object with an empty data set for: '.$table);
        }
        
        public function keySignature()
        {
            $keys = $this->table_inspector->primaryKeys();
            $sig = array();

            foreach($keys as $key_name)
            {
                $table_key_name = $this->table_inspector->name().'.'.$key_name;

                if(array_key_exists($key_name,$this->data))
                    $sig[] = $this->data[$key_name];
                else if(array_key_exists($table_key_name,$this->data))
                    $sig[] = $this->data[$table_key_name];
                else
                        throw new InvalidQueryRowException('You can\'t get a key signature for: '.$this->table_inspector->name().' from row: '.implode('::',array_keys($this->data)).' because: '.$key_name.' is not present');

            }
            
            return implode('::',$sig);
        }

        public function __get($name)
        {
            $table_name = $this->table_inspector->name().'.'.$name;

            if(array_key_exists($name,$this->data))
                $ret = $this->data[$name];
            else if(array_key_exists($table_name,$this->data))
                $ret = $this->data[$table_name];
            else
            {
                try
                {
                    $ret = new QueryIterator($this->query,$name,$this->index);
                    $pairs = array();
                    
                    foreach($this->table_inspector->primaryKeys() as $name)
                    {
                        if(isset($this->data[$name]))
                            $pairs[$name] = $this->data[$name];
                        else
                        {
                            $table_name = $this->table_inspector->name().'.'.$name;
                            
                            if(isset($this->data[$table_name]))
                                $pairs[$table_name] = $this->data[$table_name];
                        }
                    }

                    $ret->constrainKeys($pairs);
                }
                
                catch(TableInspectorException $exc)
                {
                    throw new InvalidQueryRowException('You tried to get the attribute: '.$name.' from a query row for the table: '.$this->table_inspector->name().' but this is neither a valid column nor a valid table in the database (so I can\'t return a new iterator either)');
                }
            }
            
            return $ret;
        }
    }

    class InvalidQueryRowException extends Exception {}
