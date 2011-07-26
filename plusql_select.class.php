<?php
    class PlusqlSelect
    {
        private $tables;
        private $target;
        private $initial_table;
        private $connection;
        
        public function __construct(Connection $connection)
        {
            $this->tables = array();
            $this->target = NULL;
            $this->initial_table = NULL;
            $this->connection = $connection;
        }
        
        public function __call($name,$args)
        {
            if(!count($args))
                throw new InvalidReturnSelector('You have used a method call in your from clause without passing in the name of a table to return');

            return $this->fromClause($name,$args[0]);
        }
        
        private function fromClause($name,$return = NULL)
        {
            $previous = NULL;

            if($this->target !== NULL)
                $previous = $this->target;

            if(!isset($this->tables[$name]))
            {
                $this->tables[$name] = new PlusqlTable($name);
                
                if($previous)
                    $previous->joinTable($this->tables[$name]);
            }
            
            if(!$return)
                $this->target = $this->tables[$name];
            else
            {
                if(!isset($this->tables[$return]))
                    throw new InvalidReturnSelectorException('You can\'t ask for '.$return.' in your from clause, because you haven\'t already accessed it');

                $this->target = $this->tables[$return];
            }
            

            if($this->initial_table === NULL)
                $this->initial_table = $this->target;

            return $this;
        }

        public function __get($name)
        {
            return $this->fromClause($name);
        }
        
        public function _()
        {
            $clause = '';
            $stack = array($this->initial_table);
            
            while($t = array_pop($stack))
            {
                if(!$clause)
                    $clause  = 'from '.$t->name();

                foreach($t->joinTo() as $t2)
                {
                    try
                    {
                        $on      = new OnClause($this->connection->link(),$t->name(),$t2->name());
                        $onstring = $on->toString();
                        $clause .= ' '.$t2->joinType().' '.$t2->name().' ON '.$onstring;
                    }
                    
                    catch(ManyToManyJoinException $exc)
                    {
                        $left_on = new OnClause($this->connection->link(),$t->name(),$exc->joiningTable()->name());
                        $right_on  = new OnClause($this->connection->link(),$exc->joiningTable()->name(),$t2->name());
                        $clause  .= ' '.$t2->joinType().' '.$exc->joiningTable()->name().' ON '.$left_on->toString();
                        $clause  .= ' '.$t2->joinType().' '.$t2->name().' ON '.$right_on->toString();
                    }

                    $stack[] = $t2;
                }
            }
            
            die($clause.PHP_EOL);
        }
        
        // default inner join, allow left call to override
        // allow custom on clause
        // should automatically detect mapping tables
        // let's create a way to retrieve a table previously added
    }
