<?php
    namespace PluSQL;
    
    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        $conn = new Connection('localhost','plusql','plusql','plusql');
        $conn->connect();
        $query = new Query('SELECT * FROM strong_guy',$conn->link());
        
        if($query->strong_guy instanceof QueryIterator)
            $runner->pass();
        else
            $runner->fail('Did not get a QueryIterator for a table we should have been able to');
        
        try
        {
            $query->non_existant;
            $runner->fail('Why was I able to try and get a non-existant member variable that isn\'t a valid table or field?');
        }
        
        catch(TableInspectorException $exc)
        {
            $runner->pass();
        }
        
        $row = $query->nextRow();
        
        if(count($row))
            $runner->fail('You called nextRow() on a query from which you\'d already extracted a QueryIterator - you should only be able to call rowAtIndex');

        $row = $query->rowAtIndex(0);

        if($row['strong_name'] == 'Strongy Strongo')
            $runner->pass();
        else
            $runner->fail('The row returned from your query did not contain the right data');

        $query = new Query('SELECT * FROM strong_guy',$conn->link());
        $row = $query->nextRow();
        
        if($row['strong_name'] == 'Strongy Strongo')
            $runner->pass();
        else
            $runner->fail('The row returned from your query did not contain the right data');
    });
