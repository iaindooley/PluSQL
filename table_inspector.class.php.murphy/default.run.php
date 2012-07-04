<?php
    namespace PluSQL;
    
    /**
    * Test that the same table object is returned when called for the same table
    */
    \Murphy\Test::add(function($runner)
    {
        $conn = NULL;
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')
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

    /**
    * Test that a different able is returned for the same name with different database links
    */
    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
        $conn1 = new Connection('localhost','plusql_one','plusql_one','plusql_one');
        $conn2 = new Connection('localhost','plusql_two','plusql_two','plusql_two');
        $one = TableInspector::forTable('strong_guy',$conn1->link());
        $two = TableInspector::forTable('strong_guy',$conn2->link());
        
        if($one !== $two)
            $runner->pass();
        else
            $runner->fail('TableInspector::forTable not returning the same object for the same table on the same link');
    });
