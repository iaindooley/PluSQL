<?php
    namespace plusql;

    class TableInspector
    {
        private $workers;
        private static $instance = NULL;

        private function __construct()
        {
            $this->workers = array();
        }
        
        public static function forTable($table_name,$link)
        {
            if(self::$instance === NULL)
                self::$instance = new TableInspector();

            if(!isset(self::$instance->workers[$table_name]))
                self::$instance->workers[$table_name] = new TableInspectorWorker($table_name,$link);
            
            return self::$instance->workers[$table_name];
        }
    }
