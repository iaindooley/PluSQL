<?php
    namespace plusql;
    use Plusql,mysqli;

    /**
    * Testing the quoting behaviour and default values
    */
    \murphy\Test::add(function($runner)
    {
        $sample_values = array(3,
                               3.5,
                               '3,5',
                               3.55,
                               20120101131313,
                               20120101,
                               '2012-01-01 13:13:13',
                               0,
                               '',
                               'this is a string',
                               '1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\'\"\'\'\\\'\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN',
                              );

        $sample_queries = array('INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(3,\'3\',\'3\',3,3,3,3,3,3,\'3\',\'3\',\'3\',\'3\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(3,\'3.5\',\'3.5\',3,3,3.5,3.5,3.5,3.5,\'3.5\',\'3.5\',\'3.5\',\'3.5\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(3,\'3,5\',\'3,5\',3,3,35,35,35,35,\'3,5\',\'3,5\',\'3,5\',\'3,5\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(3,\'3.55\',\'3.55\',3,3,3.55,3.55,3.55,3.55,\'3.55\',\'3.55\',\'3.55\',\'3.55\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(20120101131313,\'20120101131313\',\'20120101131313\',20120101131313,20120101131313,20120101131313,20120101131313,20120101131313,20120101131313,\'20120101131313\',\'20120101131313\',\'20120101131313\',\'20120101131313\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(20120101,\'20120101\',\'20120101\',20120101,20120101,20120101,20120101,20120101,20120101,\'20120101\',\'20120101\',\'20120101\',\'20120101\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(2012,\'2012-01-01 13:13:13\',\'2012-01-01 13:13:13\',2012,2012,20120101131313,20120101131313,20120101131313,20120101131313,\'2012-01-01 13:13:13\',\'2012-01-01 13:13:13\',\'2012-01-01 13:13:13\',\'2012-01-01 13:13:13\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(0,\'0\',\'0\',0,0,0,0,0,0,\'0\',\'0\',\'0\',\'0\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(0,\'\',\'\',0,0,0,0,0,0,\'\',\'\',\'\',\'\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(0,\'this is a string\',\'this is a string\',0,0,0,0,0,0,\'this is a string\',\'this is a string\',\'this is a string\',\'this is a string\')',
                                'INSERT INTO `type_test`(`int_auto_field`,`varchar_field_default_null`,`varchar_field_default_something`,`int_field_default_10`,`int_field_default_null`,`float_field_default_null`,`float_field_default_2point5`,`double_field_default_null`,`double_field_default_2point555`,`decimal_field_default_null`,`decimal_field_default_10point2`,`datetime_field_default_null`,`datetime_field_default_something`) VALUES(1234567890,\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\',\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\',1234567890,1234567890,1234567890098765434567.33333333333333330987654321,1234567890098765434567.33333333333333330987654321,1234567890098765434567.33333333333333330987654321,1234567890098765434567.33333333333333330987654321,\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\',\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\',\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\',\'1234567890-09876543456y7uioijuhygfdszxcvbnjml,;/.,mn,.,mnbnm\\\'\\\\\\"\\\'\\\'\\\\\\\'\\\\\\\\\\"[]}{].......3.3.3.3.3.3.3.3.3.3.3.3.3.3.3.3=-0987654321!@#$%^&*(~~!@#$%^&*()+_)OP{}|}{P;\\\';lkl;KJHGVBCDRFGHbvcxszaswsadfGVBHN\')',
                               );
                               
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
        
        foreach($sample_values as $index => $sv)
        {
            $expected_query = $sample_queries[$index];
            \murphy\Fixture::load(dirname(__FILE__).'/fixture.php')->execute();
            $to = 'plusql';
            $conn = new Connection('localhost',$to,$to,$to);
            $conn->connect();
            $ins = new Insert($conn);
            $cur_array = array();
            
            foreach($field_names as $fn)
                $cur_array[$fn] = $sv;
            
            $sql = $ins->type_test($cur_array)->filter()->insertSql();
            //WE SHOULDN'T GET ANY SQL ERRORS - INJECTION FREE
            $conn->query($sql);
            
            if($sql !== $expected_query)
                $runner->fail('The sample value: '.$sv.' produced an unexpected query result: '.$sql.' !== '.$expected_query);
            else
                $runner->pass();
        }
        
        $cur_array = array('does','not','exist');
        $ins = new Insert($conn);

        try
        {
            echo 'is: '.$ins->type_test($cur_array)->filter()->insertSql();
            $runner->fail('Why did we not get an exception of type InvalidInsertQueryException?');
        }
        
        catch(InvalidInsertQueryException $exc)
        {
            $runner->pass();
        }
    });
