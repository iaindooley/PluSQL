<?php
    namespace PluSQL;

    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
        $conn = new Connection('localhost','plusql','plusql','plusql');
        $conn->connect();

        //test strong to weak
        $clause = new OnClause($conn->link(),'strong_guy','weak_guy');
        
        if($clause->toString() == 'strong_guy.strong_guy_id = weak_guy.strong_guy_id')
            $runner->pass();
        else
            $runner->fail('The on clause for strong -> weak dependency isn\'t working right');

        //test weak to strong
        $clause = new OnClause($conn->link(),'weak_guy','strong_guy');

        if($clause->toString() == 'weak_guy.strong_guy_id = strong_guy.strong_guy_id')
            $runner->pass();
        else
            $runner->fail('The on clause for weak -> strong dependency isn\'t working right');

        //test foreign
        $clause = new OnClause($conn->link(),'weak_guy','french_guy');

        if($clause->toString() == 'weak_guy.french_guy_id = french_guy.french_guy_id')
            $runner->pass();
        else
            $runner->fail('The on clause for foreign relationships isn\'t working right');

        //test many to many join with composite primary key
        try
        {
            $clause = new OnClause($conn->link(),'weak_guy','rogue_guy');
            $clause->toString();
            $runner->fail('You should have seen a ManyToManyJoinException for weak -> rogue guy');
        }
        
        catch(ManyToManyJoinException $exc)
        {
            if($exc->joiningTable()->name() == 'is_rogue')
                $runner->pass();
            else
                $runner->fail('You got a many to many join for the weak -> rogue guys but it wasn\'t the right table, it was: '.$exc->joiningTable());
        }
        
        //we should not be able to join strong guy directly to rogue guy
        try
        {
            $clause = new OnClause($conn->link(),'strong_guy','rogue_guy');
            $clause->toString();
            $runner->fail('Why were we able to join strong_guy directly to rogue_guy?');
        }
        
        catch(UnableToDetermineOnClauseException $exc)
        {
            $runner->pass();
        }
    });
