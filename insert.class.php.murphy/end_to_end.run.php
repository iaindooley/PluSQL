<?php
    namespace PluSQL;
    use Plusql,mysqli;

    \Murphy\Test::add(function($runner)
    {
        \Murphy\Fixture::load(dirname(__FILE__).'/../on_clause.class.php.murphy/fixture.php')->execute();
        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));
/*
        mysql_query('TRUNCATE weak_guy');
        //LOOP THROUGH
        $for_strong_guy = 1;
        $stmt = Plusql::into('live');
        $names = array('Iain\'s','Another name');
        
        foreach($names as $name)
            $stmt->weak_guy(array('strong_guy_id' => $for_strong_guy,'weak_name' => $name));
        
        $stmt->insert();
        $expected = array('1:1:Iain\'s',
                          '2:1:Another name');
        $actual = array();
        
        foreach(Plusql::from('live')->weak_guy->select('weak_guy_id,strong_guy_id,weak_name')->run()->weak_guy as $row)
            $actual[] = $row->weak_guy_id.':'.$row->strong_guy_id.':'.$row->weak_name;
        
        if(serialize($expected) != serialize($actual))
            $runner->fail('Did not get the expected data back out after inserting multiple');
        else
            $runner->pass();
*/
        mysql_query('TRUNCATE weak_guy');
        //LET'S TRY A JAGGED ARRAY
        $one = array('weak_name' => 'Only the name','french_guy_id' => 10);
        $two = array('strong_guy_id' => 1,'weak_name' => 'With strong id');
        $ins = Plusql::into('live');
        $ins->weak_guy($one);
        $ins->weak_guy($two);
        $ins->insert();
        $expected = array('0:1:Only the name:10',
                          '1:1:With strong id:0');
        $actual   = array();

        foreach(Plusql::from('live')->weak_guy->select('strong_guy_id,weak_guy_id,weak_name,french_guy_id')->run()->weak_guy as $wg)
            $actual[] = $wg->strong_guy_id.':'.$wg->weak_guy_id.':'.$wg->weak_name.':'.$wg->french_guy_id;
        
        if(serialize($expected) != serialize($actual))
            $runner->fail('Did not get the expected data back out after inserting jagged');
        else
            $runner->pass();

        mysql_query('TRUNCATE weak_guy');
        //YOU CAN ALSO JUST INSERT A SINGLE RECORD
        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //DEFAULTS TO mysql_real_escape_string OR mysqli_real_escape_string AS REQUIRED
        ->insert();
        
        if(Plusql::from('live')->weak_guy->select('strong_guy_id,weak_guy_id,weak_name')->run()->weak_guy->weak_name != 'Ron Weakly')
            $runner->fail('Did not get expected value back after single insert');
        else
            $runner->pass();

        mysql_query('TRUNCATE weak_guy');
        Plusql::into('live')->weak_guy(array('strong_guy_id' => 1,
                                             'weak_name'     => 'Ron Weakly'))
        //CAN ALSO FILTER WITH A CUSTOM FUNCTION
        ->insert(function($link,$field,$value)
        {
            if($link instanceof mysqli)
                $ret = $link->escape_string($value);
            else
                $ret = mysql_real_escape_string($value,$link);
            
            if($field['Type'] != 'int(10)')
                $ret = '\''.$ret.'\'';

            return $ret;
        });

        if(Plusql::from('live')->weak_guy->select('strong_guy_id,weak_guy_id,weak_name')->run()->weak_guy->weak_name != 'Ron Weakly')
            $runner->fail('Did not get expected value back after single insert with custom filter');
        else
            $runner->pass();
    });
