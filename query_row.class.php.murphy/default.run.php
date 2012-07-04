<?php
    namespace PluSQL;
    /**
    * Test accessing fields and related tables, both existing and non-existant
    */
    \Murphy\Test::add(function($runner)
    {
        $conn = NULL;
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        \Murphy\Fixture::load(dirname(__FILE__).'/../query_iterator.class.php.murphy/fixture.php')
        ->execute(function($aliases) use(&$conn)
        {
            $deets = $aliases['plusql'];
            $conn = new Connection($deets[0],
                                   $deets[1],
                                   $deets[2],
                                   $deets[3]);
            $conn->connect();
        });

        $query = new Query('SELECT * FROM strong_guy',$conn->link());
        $row = new QueryRow($query,'strong_guy',0);
        
        if($row->keySignature() == 2)
            $runner->pass();
        else
            $runner->fail('Got the incorrect key signature for the strong_guy at index 0');

        if($row->strong_name == 'Strong 1')
            $runner->pass();
        else
            $runner->fail('Got the wrong strong_name for strong_guy at index 0');

        $query = new Query('SELECT * FROM weak_guy',$conn->link());
        $row = new QueryRow($query,'weak_guy',0);
        
        if($row->keySignature() == '2::1')
            $runner->pass();
        else
            $runner->fail('Did not get correct key signature for weak_guy at index 0');
    
        try
        {   
            $row->nothing_here;
            $runner->fail('Why was I able to get something that does not exist?');
        }
    
        catch(InvalidQueryRowException $exc)
        {   
            $runner->pass();
        }
    
        if($row->weak_name == 'Weak 1')
            $runner->pass();
        else
            $runner->fail('Did not get the correct name for weak_name at index 0');
    
        try
        {   
            $new_iterator = $row->strong_guy;
            $runner->pass();
        }
    
        catch(InvalidQueryRowException $exc)
        {   
            $runner->fail('We should have been able to get an iterator for strong_guy because the id was present');
        }
    });
