<?php
    namespace PluSQL;
    use Exception,Closure,mysqli,Bind;
    
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
            $use = array();
            $fields = $this->table->allFields();
            //WE ONLY WANT TO USE FIELDS WE KNOW ABOUT
            foreach($fields as $f)
            {
                if(isset($args[0][$f['Field']]))
                    $use[$f['Field']] = $args[0][$f['Field']];
            }
            
            $this->values[] = $use;
            return $this;
        }
        
        /**
        * Filter the last set of values that were added for insertion using mysql_real_escape_string
        * by default or optionally a closure
        */
        private function filter(Closure $filter = NULL)
        {
            $ret = array();

            foreach($this->values as $cur_values)
            {
                $cur_array = array();
                
                foreach($this->table->allFields() as $f)
                {
                    if(isset($cur_values[$f['Field']]))
                        $cur_array[$f['Field']] = $this->filterValueForField($f,$cur_values[$f['Field']],$filter);
                }
                
                $ret[] = $cur_array;
            }
            
            return $ret;
        }
        
        public function filterValueForField($f,$value,Closure $filter = NULL)
        {
            if($filter)
                $value = $filter($this->conn->link(),$f,$value);
            //BY DEFAULT, ESCAPE THE STRING, THEN CAST TO APPROPRIATE TYPE, ADDING QUOTES AS REQUIRED
            else
                $value = Bind::filterValueForField($this->conn->link(),$f,$value);
            
            return $value;
        }
        
        public function insert(Closure $filter = NULL)
        {
            return $this->conn->query($this->insertSql($filter));
        }

        public function insertSql(Closure $filter = NULL)
        {
            return 'INSERT '.$this->baseSql($filter);
        }

        public function replace(Closure $filter = NULL)
        {
            return $this->conn->query($this->replaceSql($filter));
        }

        public function replaceSql(Closure $filter = NULL)
        {
            return 'REPLACE '.$this->baseSql($filter);
        }
        
        private function baseSql(Closure $filter = NULL)
        {
            $values = $this->filter($filter);
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
            },$values);

            $used_fields = array_keys($used_fields);
            $unused = array_diff($field_names,$used_fields);
            $obj = $this;

            $final_values = array_map(function($element) use($field_names,$unused,$indexed,$obj,$filter)
            {
                foreach($unused as $rem)
                    unset($element[array_search($rem,$field_names)]);

                foreach($element as $index => $value)
                {
                    if($value === NULL)
                    {
                        $f = $indexed[$field_names[$index]];
                        $element[$index] = $obj->filterValueForField($f,$f['Default'],$filter);
                    }
                }
                   
                return '('.implode(',',$element).')';
            },$value_arrays);

            if(!count($used_fields))
                throw new InvalidInsertQueryException('I was unable to build baseSql() because there were no used fields');
            
            $final = array();

            foreach($field_names as $fn)
            {
                if(array_search($fn,$used_fields) !== FALSE)
                    $final[] = $fn;
            }
            
            return 'INTO `'.$this->table->name().'`(`'.implode('`,`',$final).'`) VALUES'.implode(',',$final_values);
        }
    }

    class InvalidInsertArgumentsException extends Exception{}
    class InvalidInsertQueryException extends Exception{}
