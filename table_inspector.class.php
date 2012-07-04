<?php
    namespace PluSQL;

    class TableInspector
    {
        private $workers;
        private $links;
        private static $instance = NULL;

        private function __construct()
        {
            $this->workers = array();
            $this->links = array();
        }
        
        public static function forTable($table_name,$link)
        {
            if(self::$instance === NULL)
                self::$instance = new TableInspector();

            if(($key = array_search($link,self::$instance->links,TRUE)) === FALSE)
            {
                $key = microtime(true).rand(0,9999);
                self::$instance->links[$key] = $link;
            }

            if(!isset(self::$instance->workers[$table_name.$key]))
                self::$instance->workers[$table_name.$key] = new TableInspectorWorker($table_name,$link);
            
            return self::$instance->workers[$table_name.$key];
        }
    }
