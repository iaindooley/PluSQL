<?php
    namespace plusql;

    class Table
    {
        private $name;
        private $join_to;
        private $join_type;
        const INNER_JOIN = 'INNER JOIN';
        const NUMERIC = 'Numeric';
        const STRING = 'String';
        const DATE = 'Date';
        static $field_types = array('TINYINT' => 'Numeric',
                                    'SMALLINT' => 'Numeric',
                                    'MEDIUMINT' => 'Numeric',
                                    'INT' => 'Numeric',
                                    'BIGINT' => 'Numeric',
                                    'FLOAT' => 'Numeric',
                                    'FLOAT' => 'Numeric',
                                    'DOUBLE' => 'Numeric',
                                    'DECIMAL' => 'Numeric',
                                    'BIT' => 'Bit',
                                    'CHAR' => 'String',
                                    'VARCHAR' => 'String',
                                    'TINYTEXT' => 'String',
                                    'TEXT' => 'String',
                                    'MEDIUMTEXT' => 'String',
                                    'LONGTEXT' => 'String',
                                    'BINARY' => 'String',
                                    'VARBINARY' => 'String',
                                    'TINYBLOB' => 'String',
                                    'BLOB' => 'String',
                                    'MEDIUMBLOB' => 'String',
                                    'LONGBLOB' => 'String',
                                    'ENUM' => 'String',
                                    'SET' => 'String',
                                    'DATE' => 'Date',
                                    'DATETIME' => 'Date',
                                    'TIME' => 'Date',
                                    'TIMESTAMP' => 'Date',
                                    'YEAR' => 'Date',
                                   );

        public function __construct($name)
        {
            $this->name    = $name;
            $this->join_to = array();
            $this->join_type = NULL;
        }
       
        /**
        * @param f - an array returned as a part of a DESCRIBE query
        */
        public static function fieldRequiresQuotesForValue($f,$value)
        {
            $ret = TRUE;

            if($value instanceof SqlFunction)
                $ret = FALSE;
            else if($value === 'NULL')
                $ret = FALSE;
            else if(self::fieldIsOfType($f,self::NUMERIC))
                $ret = FALSE;
            
            return $ret;
                
        }
        
        /**
        * @param f - an array returned as a part of a DESCRIBE query
        */
        public static function fieldIsOfType($f,$type)
        {
            return (self::$field_types[strtoupper(preg_replace('/[^A-Za-z]/','',$f['Type']))] === $type);
        }

        public function name()
        {
            return $this->name;
        }

        public function joinType()
        {
            return $this->join_type;
        }

        public function setJoinType($type)
        {
            $this->join_type = $type;
        }

        public function joinTo()
        {
            return $this->join_to;
        }

        public function joinTable(Table $table)
        {
            $this->join_to[$table->name()] = $table;
            $table->setJoinType(self::INNER_JOIN);
        }
    }
