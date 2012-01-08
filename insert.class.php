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
                    else if(!$value)
                        $value = 0;
                    
                    $last_values[$f['Field']] = $value;
                }
            }
            
            return $this;
        }
        
        public function insert()
        {
            return $this->conn->query($this->insertSql());
        }

        public function insertSql()
        {
            return 'INSERT '.$this->baseSql();
        }

        public function replace()
        {
            return $this->conn->query($this->replaceSql());
        }

        public function replaceSql()
        {
            return 'REPLACE '.$this->baseSql();
        }
        
        private function baseSql()
        {
            $all_fields = $this->table->allFields();
            $indexed    = array();

            foreach($all_fields as $f)
                $indexed[$f['Field']] = $f;

            
            $field_names = array_keys($indexed);
            $used_fields = array();
            $value_arrays = array();
            
            $value_arrays = array_map(function($element) use($field_names,&$used_fields)
            {
                $cur_array = array_fill(0,count($field_names),NULL);
                
                foreach($element as $fname => $value)
                {
                    $position = array_search($fname,$field_names);
                    $cur_array[$position] = $value;
                    $used_fields[$fname] = 1;
                }
                
                return $cur_array;
            },$this->values);

            $used_fields = array_keys($used_fields);
            $unused = array_diff($field_names,$used_fields);
            
            $final_values = array_map(function($element) use($field_names,$unused,$indexed)
            {
                foreach($unused as $rem)
                    unset($element[array_search($rem,$field_names)]);

                foreach($element as $name => $value)
                    if($value === NULL)
                        $element[$name] = $indexed[$name]['Default'];
                   
                return '('.implode(',',$element).')';
            },$value_arrays);
            
            return 'INTO `'.$this->table->name().'`(`'.implode('`,`',$used_fields).'`) VALUES'.implode(',',$final_values);
        }
    }

    class InvalidInsertArgumentsException extends Exception{}
