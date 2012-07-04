<?php
    namespace PluSQL;
    use Exception,mysqli;

    class OnClause
    {
        private $left;
        private $right;
        private $link;
        private $join_type;
        
        public function __construct($link,$left,$right,$join_type = Table::INNER_JOIN)
        {
            $this->left      = TableInspector::forTable($left,$link);
            $this->right     = TableInspector::forTable($right,$link);
            $this->link      = $link;
            $this->join_type = $join_type;
        }
        
        public function toString()
        {
            $left_fields  = $this->left->allFields();
            $right_fields = $this->right->allFields();
            $this->mapFields($left_map,$left_names,$left_fields);
            $this->mapFields($right_map,$right_names,$right_fields);
            $intersection = array_intersect($left_names,$right_names);
            //NOW TEST IF THE COMPLETE KEY OF EITHER EXISTS IN THE INTERSECTION OF FIELDS
            //IF THE COMPLETE KEY OF EITHER IS PRESENT IN IT'S ENTIRETY IN THE 
            //INTERSECTION, THEN WE USE THAT TO JOIN
            //IF THE PRIMARY KEY OF ONE IS LONGER THAN THE OTHER, TEST THAT FIRST
            $left_keys  = $this->left->primaryKeys();
            $right_keys = $this->right->primaryKeys();
            
            if(count($left_keys) > count($right_keys))
            {
                $first  = $left_keys;
                $second = $right_keys;
            }

            else
            {
                $second  = $left_keys;
                $first   = $right_keys;
            }

            $remainder = array_diff($first,$intersection);
            $use = NULL;
            
            if(count($remainder))
            {
                $remainder = array_diff($second,$intersection);
                
                if(!count($remainder))
                    $use = $second;
            }
            
            else
                $use = $first;

            if(!$use)
            {
                $tables = array();
                
                //OKAY THESE SUCKERS AREN'T RELATED - LET'S FIND OUT IF THERE'S A TABLE ANYWHERE CAPABLE OF JOINING THEM
                if($this->link instanceof mysqli)
                {
                    $res = $this->link->query('SHOW TABLES');
                    
                    while($row = $res->fetch_assoc())
                        $tables[] = TableInspector::forTable(current($row),$this->link);
                }
                
                else
                {
                    $res = mysql_query('SHOW TABLES',$this->link);
                    
                    while($row = mysql_fetch_assoc($res))
                        $tables[] = TableInspector::forTable(current($row),$this->link);
                }
                
                $left_right = implode(',',$left_keys).','.implode(',',$right_keys);
                $right_left = implode(',',$right_keys).','.implode(',',$left_keys);
                $relationships = array();
                
                foreach($tables as $t)
                {
                    $test = implode(',',$t->primaryKeys());
                    
                    if($test == $left_right)
                        $relationships[] = $t;
                    else if($test == $right_left)
                        $relationships[] = $t;
                    
                }

                //IF WE CAN'T FIND ONE, THROW AN EXCEPTION
                if(!count($relationships))
                    throw new UnableToDetermineOnClauseException('I could not automatically determine which fields to use when joining '.$this->left->name().' and '.$this->right->name().', and I could not find any suitable joining tables');
                //IF WE FIND MORE THAN ONE, THROW AN EXCEPTION
                if(count($relationships) > 1)
                    throw new UnableToDetermineOnClauseException('I could not automatically determine which fields to use when joining '.$this->left->name().' and '.$this->right->name().', and then I found more than one suitable joining table');

                throw new ManyToManyJoinException(current($relationships));
            }
            
            $clauses = array();

            foreach($use as $field)
                $clauses[] = $this->left->name().'.'.$field.' = '.$this->right->name().'.'.$field;
            
            $ret = implode(' AND ',$clauses);
            
            if($this->join_type == Table::LEFT_JOIN)
            {
                $nulls = array();
                
                foreach($use as $field)
                    $nulls[] = $this->right->name().'.'.$field.' IS NULL';

                $ret = '('.$ret.' OR ('.implode(' AND ',$nulls).'))';
            }

            return $ret;
        }
        
        private function mapFields(&$map,&$names,$fields)
        {
            $map   = array();
            $names = array();
            
            foreach($fields as $f)
            {
                $map[$f['Field']] = $f['Type'];
                $names[] = $f['Field'];
            }
        }
    }
    
    class UnableToDetermineOnClauseException extends Exception{}

    class ManyToManyJoinException extends Exception
    {
        private $joining_table;
        
        public function __construct(TableInspectorWorker $joining_table)
        {
            parent::__construct('We have a many to many join');
            $this->joining_table = $joining_table;
        }
        
        public function joiningTable()
        {
            return $this->joining_table;
        }
    }
