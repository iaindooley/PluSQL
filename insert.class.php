<?php
    namespace plusql;
    use Exception,Closure,mysqli;
    
    class Insert
    {
        private $conn;
        private $table;
        private $values;

        public function __construct(Connection $conn)
        {
            $this->conn   = $conn;
            $this->table  = NULL;
            $this->values = array();
        }
        
        public function __call($name,$args)
        {
            if(!is_array($args) || (count($args) > 1))
                throw new InvalidInsertArgumentsException('When you call a method on Insert you should pass in an array of key/value pairs to be inserted');
            
            $this->table = TableInspector::forTable($name,$this->conn->link());
            $this->values[] = $args[0];
            return $this;
        }
        
        /**
        * Filter the last set of values that were added for insertion using mysql_real_escape_string
        * by default or optionally a closure
        */
        public function filter(Closure $filter = NULL)
        {
            $last_values = &$this->values[count($this->values) - 1];
            
            foreach($this->table->allFields() as $f)
            {
                if(isset($last_values[$f['Field']]))
                {
                    $value = $last_values[$f['Field']];
                    $do_quotes = FALSE;

                    if(Table::fieldRequiresQuotesForValue($f,$value))
                        $do_quotes = TRUE;

                    if($filter)
                        $value = $filter($this->conn->link(),$f['Field'],$value);
                    else
                    {
                        if($this->conn->link() instanceof mysqli)
                            $value = $this->conn->link()->escape_string($value);
                        else
                            $value = mysql_real_escape_string($value,$this->conn->link());
                    }
    
                    if($do_quotes)
                        $value = '\''.$value.'\'';
                    
                    $last_values[$f['Field']] = $value;
                }
            }
            
            return $this;
        }
        
        public function insert()
        {
print_r($this->values);
        }

        public function replace()
        {
            die('building a replace query');
        }
    }

    class InvalidInsertArgumentsException extends Exception{}
