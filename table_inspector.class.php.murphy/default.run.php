<?php
    namespace plusql;
    
    \murphy\Test::add(function($runner)
    {
        $conn = NULL;
        \murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')
        ->also(dirname(__FILE__).'/../query_iterator.class.php.murphy/fixture.php')
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

        $one = TableInspector::forTable('strong_guy',$conn->link());
        $two = TableInspector::forTable('strong_guy',$conn->link());
        
        if($one === $two)
            $runner->pass();
        else
            $runner->fail('TableInspector::forTable not returning the same object for the same table on the same link');
    });
