    \murphy\Test::add(function($runner)
    {
        \murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
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
            
            return $ret;
        })->insert();
    });
