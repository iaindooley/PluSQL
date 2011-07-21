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
                if(!$query = mysql_query('DESCRIBE '.$this->table_name,$this->link))
                    throw new TableInspectorException('It looks like: '.$this->table_name.' doesn\'t exist');
                $this->primary_keys = array();
    
                while($row = mysql_fetch_assoc($query))
                {
                    if($row['Key'] == 'PRI')
                        $this->primary_keys[] = $row['Field'];
                }
            }
            
            return $this->primary_keys;
        }
    }

    class TableInspectorException extends Exception {}
