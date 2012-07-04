<?php
    namespace PluSQL;
    use Exception,Closure,mysqli,Bind;
    
    class Update
    {
        private $conn;
        private $table;
        private $values;
        private $where;
        const ENTIRE_TABLE = 'ENTIRE_TABLE';

        public function __construct(Connection $conn)
        {
            $this->conn   = $conn;
            $this->table  = NULL;
            $this->values = NULL;
            $this->where  = NULL;
        }
        
        public function usedFilter()
        {
            return $this->filter;
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
            
            $this->values = $use;
            return $this;
        }
        
        /**
        * Filter the last set of values that were added for insertion using mysql_real_escape_string
        * by default or optionally a closure
        */
        private function filter(Closure $filter = NULL)
        {
            $ret = array();

            foreach($this->table->allFields() as $f)
            {
                if(isset($this->values[$f['Field']]))
                    $ret[$f['Field']] = $this->filterValueForField($f,$this->values[$f['Field']],$filter);
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
        
        public function update(Closure $filter = NULL)
        {
            return $this->conn->query($this->updateSql($filter));
        }
        
        public function where($clause)
        {
            $this->where = $clause;
            return $this;
        }

        public function updateSql(Closure $filter = NULL)
        {
            $values = $this->filter($filter);
            
            if($this->where == NULL)
                throw new UnsafeUpdateException('We are stopping you from accidentally updating every row in your table. If you really wanted to update every row, then call where(Update::ENTIRE_TABLE)');
            
            $sql = 'UPDATE `'.$this->table->name().'` SET ';
            $imp = array();
            
            foreach($values as $name => $value)
                $imp[] = '`'.$name.'` = '.$value;
            
            $sql .= implode(',',$imp);
            
            if($this->where !== self::ENTIRE_TABLE)
                $sql .= ' WHERE '.$this->where;
            
            return $sql;
        }
    }

    class UnsafeUpdateException extends Exception{}
