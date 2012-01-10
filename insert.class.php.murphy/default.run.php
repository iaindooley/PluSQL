<?php
    namespace plusql;
    use Plusql;

    /**
    * Testing the basic insert query building
    */
    \murphy\Test::add(function($runner)
    {
        \murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        $to = 'plusql';
        $conn = new Connection('localhost',$to,$to,$to);
        $conn->connect();

        $ins = new Insert($conn);
        //THE ARRAY PASSED SHOULD BE ABLE TO HAVE KEYS THAT DON'T EXIST IN GIVEN ENTITY
        $ins->weak_guy(array('strong_guy_id' => 1,
                             'weak_name'     => 'Weaky Weakling\'s',
                             'nothing'       => 'Nowhere',
                             ))->filter();

        $test = 'INSERT INTO `weak_guy`(`strong_guy_id`,`weak_name`) VALUES(1,\'Weaky Weakling\\\'s\')';
        
        if($ins->insertSql() == $test)
            $runner->pass();
        else
            $runner->fail('Insert sql incorrectly rendered: '.$ins->insertSql());
        
        //SHOULD BE ABLE TO GENERATE THE SQL A BUNCH OF TIMES
        if($ins->insertSql() == $test)
            $runner->pass();
        else
            $runner->fail('Insert sql was rendered differently second time around');

        $ins = new Insert($conn);
        $filter = function($link,$name,$value)
        {
            return str_replace('2nd','3rd',$value);
        };
        $ins->weak_guy(array('strong_guy_id' => 1,
                             'weak_name'     => 'Winkly Weakling The 2nd'))
        //CAN ALSO PROVIDE CUSTOM FILTER FUNCTION THAT ACCEPTS $link,$name,$value
        ->filter($filter);
        
        $test = 'INSERT INTO `weak_guy`(`strong_guy_id`,`weak_name`) VALUES(1,\'Winkly Weakling The 3rd\')';

        if($ins->insertSql() === $test)
            $runner->pass();
        else
            $runner->fail('Unable to provide custom filter');
    });
    
    \murphy\Test::add(function($runner)
    {
        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));
        Plusql::credentials('dev',array('localhost','plusql','plusql','plusql_dev'));
//LOOP THROUGH
$for_strong_guy = 1;
$stmt = Plusql::into('live');
$names = array('Iain\'s',new SqlFunction('now()'),);

foreach($names as $name)
    $stmt->weak_guy(array('strong_guy_id' => $for_strong_guy,'weak_name' => $name))->filter();

$stmt->insert();

        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //DEFAULTS TO mysql_real_escape_string OR mysqli_real_escape_string AS REQUIRED
        ->filter()->insert();

        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //CAN ALSO FILTER WITH A CUSTOM FUNCTION
        ->filter(function($link,$name,$value)
        {
            if($link instanceof mysqli)
                $ret = $link->escape_string($value);
            else
                $ret = mysql_real_escape_string($value,$link);
        })->insert();
    });
