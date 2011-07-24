<?php
    class TableInspectorWorker
    {
        private $table_name;
        private $link;
        private $primary_keys;

        public function __construct($table_name,$link)
        {
            $this->table_name = $table_name;
            $this->link = $link;
            $this->primary_keys = NULL;
        }

        public function name()
        {
            return $this->table_name;
        }

        public function primaryKeys()
        {
            if($this->primary_keys === NULL)
            {
                $describe_sql = 'DESCRIBE '.$this->table_name;
                
                if($this->link instanceof mysqli)
                    $query = $this->link->query($describe_sql);
                else
                    $query = mysql_query($describe_sql,$this->link);

                if(!$query)
                    throw new TableInspectorException('It looks like: '.$this->table_name.' doesn\'t exist');

                $this->primary_keys = array();
    
                while($row = self::queryRow($query))
                {
                    if($row['Key'] == 'PRI')
                        $this->primary_keys[] = $row['Field'];
                }
            }
            
            return $this->primary_keys;
        }
        
        public static function queryRow($query)
        {
            if($query instanceof mysqli_result)
                $ret = $query->fetch_assoc();
            else
                $ret = mysql_fetch_assoc($query);
            
            return $ret;
        }
    }

    class TableInspectorException extends Exception {}
