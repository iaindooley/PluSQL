<?php
    /**
    * Test pushing and popping the database connection stack
    */
    murphy\Test::add(function($runner)
    {
        //run the fixture which will create our databases and populate them
        //with some sample data
        murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
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
