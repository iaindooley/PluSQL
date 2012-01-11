<?php
    namespace plusql;

    function getConnection()
    {
        $conn = NULL;
        \murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        \murphy\Fixture::load(dirname(__FILE__).'/../query_iterator.class.php.murphy/fixture.php')
        ->execute(function($aliases) use(&$conn)
        {
            $aliases = $aliases['plusql'];
            $host = $aliases[0];
            $username = $aliases[1];
            $password = $aliases[2];
            $dbname = $aliases[3];
            $conn = new Connection($host,$username,$password,$dbname);
            $conn->connect();
        });
        
        return $conn;
    }
    
    function testProperty(Connection $conn,\murphy\Test $runner,$name,$initial,$additional)
    {
        $sel = new Select($conn);
        $sel->$name($initial);
        
        if($sel->$name() == $initial)
            $runner->pass();
        else
            $runner->fail('Unable to set '.$name.' clause to '.$initial);
        
        if($additional)
        {
            $sel->$name($sel->$name().$additional);
            
            if($sel->$name() == $initial.$additional)
                $runner->pass();
            else
                $runner->fail('Unable to update '.$name.' clause to '.$initial.$additional);
        }
    }
