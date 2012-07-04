<?php
    namespace PluSQL;

    /**
    * Check the QueryIterator query row constraints functionality
    */
    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        $conn = new Connection('localhost','plusql','plusql','plusql');
        $conn->connect();
        $query = new Query('SELECT * FROM strong_guy',$conn->link());
        
        try
        {
            $iterator = new QueryIterator($query,'weak_guy');
            $runner->fail('Why did the QueryIterator with query that only contained strong_guy not throw an exception when used to access weak_guy?');
        }
        
        catch(InvalidQueryRowException $exc)
        {
            $runner->pass();
        }
        
        $iterator = new QueryIterator($query,'strong_guy');
        $pairs = array('strong_guy_id' => 1);
        $iterator->constrainKeys($pairs);
        $iterator->checkConstraints();
        $wrong_pair = array('non_existant' => 1);
        $iterator->constrainKeys($wrong_pair);
        
        try
        {
            $iterator->checkConstraints();
            $runner->fail('Why was I able to constrain an iterator for strong_guy to a field non_existant?');
        }
        
        catch(InvalidQueryRowException $exc)
        {
            $runner->pass();
        }
        
        $wrong_paid = array('strong_guy_id' => 2);
        $iterator->constrainKeys($wrong_pair);
        
        try
        {
            $iterator->checkConstraints();
            $runner->fail('Why was I able to constrain an iterator for strong_guy to value that should be wrong?');
        }
        
        catch(InvalidQueryRowException $exc)
        {
            $runner->pass();
        }
    });

    /**
    * Test using as an iterator in a foreach loop
    */
    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        $conn = new Connection('localhost','plusql','plusql','plusql');
        $conn->connect();
        $query = new Query('SELECT * FROM strong_guy',$conn->link());
        
        $iterator = new QueryIterator($query,'strong_guy',0);
        $pairs = array('strong_guy_id' => 1);
        $iterator->constrainKeys($pairs);

        if($iterator->strong_name == 'Strongy Strongo')
            $runner->pass();
        else
            $runner->fail('Could not get value for strong_name from strong_guy iterator');

        $conn = NULL;

        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')
        ->also(dirname(__FILE__).'/fixture.php')
        ->execute(function($aliases) use(&$conn)
        {
            $deets = $aliases['plusql'];
            $conn = new Connection($deets[0],
                                   $deets[1],
                                   $deets[2],
                                   $deets[3]);
            $conn->connect();
        });
        
        $expected_output = 'Strong 1:Weak 1:Rogue 1:French 1
Strong 1:Weak 2:Rogue 1:French 1
Strong 2:Weak 3:Rogue 2:French 2
Strong 2:Weak 3:Rogue 1:French 2
Strong 2:Weak 4:Rogue 2:French 2
';
        $query = new Query('SELECT * FROM strong_guy INNER JOIN weak_guy USING(strong_guy_id) INNER JOIN is_rogue USING(strong_guy_id,weak_guy_id) INNER JOIN rogue_guy USING (rogue_guy_id) INNER JOIN french_guy USING(french_guy_id) ORDER BY strong_guy_id,weak_guy_id',$conn->link());
        $iterator = new QueryIterator($query,'strong_guy',0);
$start = microtime(true);
        ob_start();

        foreach($iterator as $sg)
        {
            foreach($sg->weak_guy as $wg)
                foreach($wg->rogue_guy as $rg)
                    echo $sg->strong_name.':'.$wg->weak_name.':'.$rg->rogue_name.':'.$wg->french_guy->french_name.PHP_EOL;
        }
        
        if(ob_get_clean() == $expected_output)
            $runner->pass();
        else
            $runner->fail('The output was unexpected');
    });
