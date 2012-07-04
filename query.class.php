<?php
    namespace PluSQL;
    use EmptySetException,Exception,mysqli;

    class Query
    {
        private $query;
        private $link;
        
        public function __construct($sql,$link)
        {
            if($link instanceof mysqli)
                $query = $link->query($sql);
            else
                $query = mysql_query($sql,$link);

            if(!$query)
            {
                if($link instanceof mysqli)
                    $error = $link->error;
                else
                    $error = mysql_error();

                throw new SqlErrorException($error);
            }

            $this->query = $query;
            $this->link  = $link;
        }
        
        public function __get($name)
        {
            return new QueryIterator($this,$name);
        }
        
        public function nextRow()
        {
            if($this->link instanceof mysqli)
                $row = $this->query->fetch_assoc();
            else
                $row = mysql_fetch_assoc($this->query);

            return $row;
        }
        
        public function rowAtIndex($index)
        {
            if($this->link instanceof mysqli)
                $num_rows = $this->query->num_rows;
            else
                $num_rows = mysql_num_rows($this->query);

            if(!$num_rows)
                throw new EmptySetException('You have passed me a query that returns no information');
            if($num_rows <= $index)
                throw new InvalidQueryRowException('Out of range');

            if($this->link instanceof mysqli)
            {
                $this->query->data_seek($index);
                $ret = $this->query->fetch_assoc();
            }

            else
            {
                mysql_data_seek($this->query,$index);
                $ret = mysql_fetch_assoc($this->query);
            }

            return $ret;
        }
        
        public function link()
        {
            return $this->link;
        }
    }
    
    class SqlErrorException extends Exception{}
