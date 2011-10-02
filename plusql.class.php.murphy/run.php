<?php
    /**
    * Test for something or other
    */
    murphy\Test::add(function($runner)
    {
        murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
        $runner->pass();
        $runner->fail('You did not do something right');
    });
