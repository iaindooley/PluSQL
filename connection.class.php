<?php
    class Connection
    {
        private $credentials;
        private $link;

        public function __construct($host,$user,$password,$dbname)
        {
            $this->credentials = func_get_args();
            $this->link = mysql_connect($host,$user,$password);
        }
        
        public function connect()
        {
            mysql_select_db($this->credentials[Anorm::NAME],$this->link) or die(mysql_error());
        }
        
        public function query($sql)
        {
            return new AnormQuery($sql,$this->link);
        }
    }
