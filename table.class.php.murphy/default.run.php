<?php
    namespace PluSQL;

    \Murphy\Test::add(function($runner)
    {
        $table = new Table('strong_guy');
        
        if($table->name() == 'strong_guy')
            $runner->pass();
        else
            $runner->fail('Table name not set correctly in constructor');
        
        $table->setJoinType(Table::INNER_JOIN);
        
        if($table->joinType() == 'INNER JOIN')
            $runner->pass();
        else
            $runner->fail('Unable to set join type');
        
        $table  = new Table('strong_guy');
        $table2 = new Table('weak_guy');
        $table->joinTable($table2);
        
        if($table2->joinType() == 'INNER JOIN')
            $runner->pass();
        else
            $runner->fail('The join type was not set to INNER JOIN by default');

        ob_start();
        print_r($table->joinTo());
        $actual = ob_get_clean();
        $expected = 'Array
(
    [weak_guy] => PluSQL\Table Object
        (
            [name:PluSQL\Table:private] => weak_guy
            [join_to:PluSQL\Table:private] => Array
                (
                )

            [join_type:PluSQL\Table:private] => INNER JOIN
        )

)
';
        if($actual == $expected)
            $runner->pass();
        else
            $runner->fail('After joining a table, the resulting joinTo() was not what we expected');
    });
