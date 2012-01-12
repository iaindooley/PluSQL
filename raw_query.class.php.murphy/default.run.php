<?php
    namespace plusql;
    use EmptySetException,Plusql;

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

    \murphy\Test::add(function($runner)
    {
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
        
        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));

        if(Plusql::against('live')->run('SELECT * FROM weak_guy')->weak_guy->weak_name != 'Weaky Weakling')
            $runner->fail('Unable to get the correct result from a raw query');
        else
            $runner->pass();
    });
