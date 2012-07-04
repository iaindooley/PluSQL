<?php
    namespace PluSQL;
    use Exception,mysqli,mysqli_result;

    class TableInspectorWorker
    {
        private $table_name;
        private $link;
        private $primary_keys;
        private $all_fields;

        public function __construct($table_name,$link)
        {
            $this->table_name = $table_name;
            $this->link = $link;
            $this->primary_keys = NULL;
            $this->all_fields = NULL;
        }

        public function name()
        {
            return $this->table_name;
        }

        public function allFields()
        {
            if($this->all_fields === NULL)
            {
                $describe_sql = 'DESCRIBE '.$this->table_name;
                
                if($this->link instanceof mysqli)
                    $query = $this->link->query($describe_sql);
                else
                    $query = mysql_query($describe_sql,$this->link);

                if(!$query)
                    throw new TableInspectorException('It looks like: '.$this->table_name.' doesn\'t exist');

                $this->all_fields = array();
    
                while($row = self::queryRow($query))
                    $this->all_fields[] = $row;
            }
            
            return $this->all_fields;
        }

        public function primaryKeys()
        {
            if($this->primary_keys === NULL)
            {
                $this->primary_keys = array();

                foreach($this->allFields() as $row)
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
