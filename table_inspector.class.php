<?php
    class TableInspector
    {
        private $workers;
        private $link;
        private static $instance = NULL;

        public function __construct($link)
        {
            $this->link = $link;
            $this->workers = array();
        }
        
        public static function forTable($table_name,$link)
        {
            if(self::$instance === NULL)
                self::$instance = new TableInspector($link);
            
            if(!isset(self::$instance->workers[$table_name]))
                self::$instance->workers[$table_name] = new TableInspectorWorker($table_name,self::$instance->link);
            
            return self::$instance->workers[$table_name];
        }
    }
