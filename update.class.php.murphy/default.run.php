<?php
    namespace plusql;
    use Plusql,mysqli;

    /**
    * Testing the basic insert query building
    */
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

        $update = new Update($conn);
        $strong = array('strong_name' => 'This\'s it');
        
        try
        {
            $update->strong_guy($strong)->update();
            $runner->fail('Why were we able to update the whole table?');
        }
        
        catch(UnsafeUpdateException $exc)
        {
            $runner->pass();
        }
        
        $update->where(Update::ENTIRE_TABLE)->filter()->update();
        $sel = new Select($conn);
        $expected = array('This\'s it',
                          'This\'s it');
        $actual = array();
        
        foreach($sel->strong_guy->select('*')->run()->strong_guy as $sg)
            $actual[] = $sg->strong_name;
        
        if(serialize($expected) !== serialize($actual))
            $runner->fail('Did not manage to update the entire table');
        else
            $runner->pass();

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
        $update = new Update($conn);
        $strong = array('strong_name' => 'This\'s it');
        $update->strong_guy($strong)->where('strong_name = \'Strong 1\'')->filter()->update();
        
        $expected = array('This\'s it',
                          'Strong 2');
        $actual = array();
        
        foreach($sel->strong_guy->select('*')->run()->strong_guy as $sg)
            $actual[] = $sg->strong_name;

        
        if(serialize($expected) !== serialize($actual))
            $runner->fail('Did not manage to update Strong Name 1');
        else
            $runner->pass();
    });
