<?php
    namespace plusql;
    use EmptySetException;

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
        
        $raw = new RawQuery($conn);
        $raw->run('DELETE FROM strong_guy');
        $sel = new Select($conn);
        
        try
        {
            $sel->strong_guy->select('*')->run()->strong_guy->strong_name;
            $runner->fail('Why was an EmptySetException not thrown?');
        }
        
        catch(EmptySetException $exc)
        {
            $runner->pass();
        }
    });
