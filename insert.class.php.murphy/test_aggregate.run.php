<?php
    namespace plusql;
    use Plusql,mysqli;

    \murphy\Test::add(function($runner)
    {
        \murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
        $to = 'plusql';
        $conn = new Connection('localhost',$to,$to,$to);
        $conn->connect();
        $ins = new Insert($conn);
        $field_names = array('int_auto_field',
                     'varchar_field_default_null',
                     'varchar_field_default_something',
                     'int_field_default_10',
                     'int_field_default_null',
                     'float_field_default_null',
                     'float_field_default_2point5',
                     'double_field_default_null',
                     'double_field_default_2point555',
                     'decimal_field_default_null',
                     'decimal_field_default_10point2',
                     'datetime_field_default_null',
                     'datetime_field_default_something',
                    );

        $ins->type_test(array('varchar_field_default_null' => new SqlFunction('REPLACE(\'onetwo\',\'one\',\'two\')')))->filter()->insert();
        Plusql::credentials('live',array('localhost','plusql','plusql','plusql'));
        
        if(Plusql::from('live')->type_test->select('int_auto_field,varchar_field_default_null')->run()->type_test->varchar_field_default_null != 'twotwo')
            $runner->fail('The REPLACE aggregate function did not work');
        else
            $runner->pass();
    });
