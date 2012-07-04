<?php
    /**
    * Test pushing and popping the database connection stack
    */
    Murphy\Test::add(function($runner)
    {
        //run the fixture which will create our databases and populate them
        //with some sample data
        Murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
        //connect using live/dev credentials
        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));
        Plusql::credentials('dev',array('localhost','plusql','plusql','plusql_dev'));

        //verify that we can connect to both dev and live
        if(Plusql::connect('live')->query('SELECT * FROM fixture_data')->fixture_data->field_value == 'plusql fixture value')
            $runner->pass();
        else
            $runner->fail('The fixture value present in the live database is incorrect');

        if(Plusql::connect('dev')->query('SELECT * FROM fixture_data')->fixture_data->field_value == 'plusql_dev fixture value')
            $runner->pass();
        else
            $runner->fail('The fixture value present in the dev database is incorrect');

        //verify that we can switch back and forth as required
        if(Plusql::connect('live')->query('SELECT * FROM fixture_data')->fixture_data->field_value == 'plusql fixture value')
            $runner->pass();
        else
            $runner->fail('I was unable to switch back to the live database after querying dev');

        if(Plusql::connect('dev')->query('SELECT * FROM fixture_data')->fixture_data->field_value == 'plusql_dev fixture value')
            $runner->pass();
        else
            $runner->fail('I was unable to switch back to the dev database after switching from dev to live');
    });

    Murphy\Test::add(function($runner)
    {
        $conn = NULL;
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        \Murphy\Fixture::load(dirname(__FILE__).'/../query_iterator.class.php.murphy/fixture.php')
        ->execute(function($aliases) use(&$conn)
        {   
            $aliases = $aliases['plusql'];
            $host = $aliases[0];
            $username = $aliases[1];
            $password = $aliases[2];
            $dbname = $aliases[3];
            $conn = new plusql\Connection($host,$username,$password,$dbname);
            $conn->connect();
        });

        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));
        $f = Plusql::escape('live');
        
        $sql = (string)Plusql::from('live')->strong_guy->select('*')->where('strong_name = \''.$f('Strong Name\'s').'\'');

        if($sql == 'SELECT * FROM strong_guy WHERE strong_name = \'Strong Name\\\'s\'')
            $runner->pass();
        else
            $runner->fail('Did not get the correct value after escaping');
    });
