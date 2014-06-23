<?php
    namespace PluSQL;
    use mysqli,Plusql,Exception;

    class Connection
    {
        private $credentials;
        private $link;

        public function __construct($host,$user,$password,$dbname)
        {
            $this->credentials = func_get_args();
            $this->establishConnection($host,$user,$password,$dbname)
        }
        
        private function establishConnection($host,$user,$password,$dbname)
        {
            if(class_exists('mysqli'))
                $this->link = new mysqli($host,$user,$password);
            else
                $this->link = mysql_connect($host,$user,$password);
        }
        
        public function link()
        {
            return $this->link;
        }

        public function connect()
        {
            if($this->link instanceof mysqli)
            {
                if(!$this->link->ping())
                {
                    if(ini_get('mysqli.reconnect'))
                        throw new Exception('MySQL server connection lost and unable to reconnect');
                    else
                    {
                        $this->link->close();
                        $this->establishConnection($this->credentials[Plusql::HOST],
                                                   $this->credentials[Plusql::USER],
                                                   $this->credentials[Plusql::PASS],
                                                   $this->credentials[Plusql::NAME]);
                    }
                }

                if(!$this->link->select_db($this->credentials[Plusql::NAME]))
                    throw new Exception($this->link->error);
            }

            else
            {
                if(!mysql_ping($this->link))
                {
                    mysql_close($this->link);
                    $this->establishConnection($this->credentials[Plusql::HOST],
                                               $this->credentials[Plusql::USER],
                                               $this->credentials[Plusql::PASS],
                                               $this->credentials[Plusql::NAME]);
                }

                if(!mysql_select_db($this->credentials[Plusql::NAME],$this->link))
                    throw new Exception(mysql_error());
            }
        }

        public function escape($value)
        {
            $ret = NULL;

            if($this->link instanceof mysqli)
                $ret = $this->link->escape_string($value);
            else
                $ret = mysql_real_escape_string($value);
            
            return $ret;
        }
        
        public function query($sql)
        {
            return new Query($sql,$this->link);
        }
    }
