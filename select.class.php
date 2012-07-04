<?php
    namespace PluSQL;
    use Exception,Bind;

    class Select
    {
        private $tables;
        private $target;
        private $initial_table;
        private $connection;
        private $query_props;
        private $last_added;
        
        public function __construct(Connection $connection)
        {
            $this->tables = array();
            $this->target = NULL;
            $this->initial_table = NULL;
            $this->connection = $connection;
            $this->query_props = array();
            $this->last_added = NULL;
        }
        
        public function __call($name,$args)
        {
            if(!count($args))
                throw new InvalidReturnSelectorException('You have used a method call in your from clause without passing in the name of a table to return');
    
            return $this->fromClause($name,$args[0]);
        }
        
        private function fromClause($name,$return = NULL)
        {
            $previous = NULL;

            if($this->target !== NULL)
                $previous = $this->target;

            if(!isset($this->tables[$name]))
            {
                $this->tables[$name] = new Table($name);
                
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

            $this->last_added = $name;
            return $this;
        }
        
        public function joinType($type)
        {
            if(isset($this->tables[$this->last_added]))
                $this->tables[$this->last_added]->setJoinType($type);
            
            return $this;
        }

        public function __get($name)
        {
            $ret = $this->fromClause($name);
            return $ret;
        }
        
        public function buildFromClause()
        {
            $from_clause = '';
            $stack = array($this->initial_table);
            
            while($t = array_pop($stack))
            {
                if(!$from_clause)
                    $from_clause  = 'FROM '.$t->name();

                foreach($t->joinTo() as $t2)
                {
                    try
                    {
                        $on      = new OnClause($this->connection->link(),$t->name(),$t2->name(),$t2->joinType());
                        $onstring = $on->toString();
                        $from_clause .= ' '.$t2->joinType().' '.$t2->name().' ON '.$onstring;
                    }
                    
                    catch(ManyToManyJoinException $exc)
                    {
                        $left_on = new OnClause($this->connection->link(),$t->name(),$exc->joiningTable()->name(),$t2->joinType());
                        $right_on  = new OnClause($this->connection->link(),$exc->joiningTable()->name(),$t2->name(),$t2->joinType());
                        $from_clause  .= ' '.$t2->joinType().' '.$exc->joiningTable()->name().' ON '.$left_on->toString();
                        $from_clause  .= ' '.$t2->joinType().' '.$t2->name().' ON '.$right_on->toString();
                    }

                    $stack[] = $t2;
                }
            }
            
            return $from_clause;
        }
        
        public function run()
        {
            return $this->connection->query((string)$this);
        }

        public function __toString()
        {
            $query = 'SELECT '.$this->select().' '.$this->buildFromClause();

            if($where = $this->where())
                $query .= ' WHERE '.$where;
            
            if($group_by = $this->groupBy())
                $query .= ' GROUP BY '.$group_by;

            if($having = $this->having())
                $query .= ' HAVING '.$having;

            if($order_by = $this->orderBy())
                $query .= ' ORDER BY '.$order_by;
            
            if($limit = $this->limit())
                $query .= ' LIMIT '.$limit;
            
            return $query;
        }
        
        private function queryProperty($name,$value = NULL)
        {
            if($value !== NULL)
            {
                $this->query_props[$name] = $value;
                $ret = $this;
            }
           
            else
            {
                if(isset($this->query_props[$name]))
                    $ret = $this->query_props[$name];
                else
                    $ret = FALSE;
            }

            return $ret;
        }

        public function select($select = NULL)
        {
            return $this->queryProperty('select',$select);
        }

        public function where($where = NULL)
        {
            if($where instanceof Bind)
                $where->setLink($this->connection->link());

            return $this->queryProperty('where',$where);
        }

        public function groupBy($group_by = NULL)
        {
            return $this->queryProperty('group_by',$group_by);
        }

        public function having($having = NULL)
        {
            return $this->queryProperty('having',$having);
        }

        public function orderBy($order_by = NULL)
        {
            return $this->queryProperty('order_by',$order_by);
        }

        public function limit($limit = NULL)
        {
            return $this->queryProperty('limit',$limit);
        }
    }

    class InvalidReturnSelectorException extends Exception{}
