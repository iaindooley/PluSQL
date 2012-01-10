<?php
    namespace plusql;
    
    \murphy\Test::add(function($runner)
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
        
        $worker = new TableInspectorWorker('strong_guy',$conn->link());
        $expected = 'bd3251f7c5acccb2b73fd83be63c7ea2';

        ob_start();
        print_r($worker->allFields());
        $actual = md5(trim(ob_get_clean()));

        if($actual == $expected)
            $runner->pass();
        else
            $runner->fail('Did not get the correct field data from TableInspectorWorker');

        $worker = new TableInspectorWorker('strong_guy',$conn->link());
        $expected = '1b656c3c43833bf1ac9cf5620643522f';

        ob_start();
        print_r($worker->primaryKeys());
        $actual = md5(trim(ob_get_clean()));
        
        if($actual == $expected)
            $runner->pass();
        else
            $runner->fail('TableInspectorWorker::primaryKeys did not return the correct fields');
    });
