<?php
    class Bind
    {
        private $string;
        private $params;
        private $link;
        
        public function __construct($string,$params)
        {
            $this->string = $string;
            $this->params = $params;
            $this->link   = NULL;
        }
        
        public function setLink($link)
        {
            $this->link = $link;
        }

        public function __toString()
        {
            
        }

        public static function filterValueForField($link,$f,$value)
        {
            $do_quotes = FALSE;

            if(PluSQL\Table::fieldRequiresQuotesForValue($f,$value))
                $do_quotes = TRUE;

            if(!($value instanceof PluSQL\SqlFunction))
            {   
                if($link instanceof mysqli)
                    $value = $link->escape_string($value);
                else
                    $value = mysql_real_escape_string($value,$link);
            }
            
            if(!$do_quotes)
                $value = PluSQL\Table::stripForNumericField($f,$value);

            if($do_quotes)
                $value = '\''.$value.'\'';
            else if(!$value)
                $value = 0;
            
            return $value;
        }
    }

    class UnlinkedBindException extends Exception{}
