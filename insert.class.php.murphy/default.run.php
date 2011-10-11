<?php
    namespace plusql;
    use Plusql;
    
    \murphy\Test::add(function($runner)
    {
        \murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')
        ->execute();
        $to = 'plusql';
        $conn = new Connection('localhost',$to,$to,$to);
        $conn->connect();

        $ins = new Insert($conn);
        $ins->weak_guy(array('strong_guy_id' => 1,
                             'weak_name'     => 'Winkly Weakling'))
        //DEFAULTS TO mysql_real_escape_string OR mysqli_real_escape_string AS REQUIRED
        ->filter();
//ALSO DO SUPPORT FOR MULTI VALUE INSERTS - SIMPLIFY THE PROCESS OF CREATING THE ARRAY?
//OR JUST LET THEM DO IT THEMSELVES??

//NEED SUPPORT FOR CUMULATIVELY BUILDING THEM
        $ins = new Insert($conn);
        $ins->weak_guy(array('strong_guy_id' => 1,
                             'weak_name'     => 'Winkly Weakling The 2nd'))
        //CAN ALSO PROVIDE CUSTOM FILTER FUNCTION THAT ACCEPTS $link,$name,$value
        ->filter(function($link,$name,$value)
        {
            return mysql_real_escape_string($value);
        })
        ->insert();
    });
    
    \murphy\Test::add(function($runner)
    {
        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //DEFAULTS TO mysql_real_escape_string OR mysqli_real_escape_string AS REQUIRED
        ->filter()->insert();

        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //CAN ALSO FILTER WITH A CUSTOM FUNCTION
        ->filter(function($link,$name,$value)
        {
            return mysql_real_escape_string($name);
        })->insert();
    });
