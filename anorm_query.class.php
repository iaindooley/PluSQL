<?php
    class AnormQuery
    {
        private $query;
        private $link;
        
        public function __construct($sql,$link)
        {
            if(!$this->query = mysql_query($sql,$link))
                throw new SqlErrorException(mysql_error());

            $this->link = $link;
        }
        
        public function __get($name)
        {
            return new AnormQueryIterator($this,$name);
        }
        
        public function nextRow()
        {
            return mysql_fetch_assoc($this->query);
        }
        
        public function rowAtIndex($index)
        {
            if(!mysql_num_rows($this->query))
                throw new EmptySetException('You have passed me a query that returns no information');
            if(mysql_num_rows($this->query) <= $index)
                throw new InvalidAnormQueryRowException('Out of range');

            mysql_data_seek($this->query,$index);
            return mysql_fetch_assoc($this->query);
        }
        
        public function link()
        {
            return $this->link;
        }
    }
    
    class SqlErrorException extends Exception{}
