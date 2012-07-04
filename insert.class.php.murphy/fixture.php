<?php
    /**
    * dbname
    * plusql
    */
    Murphy\Fixture::add(function($row)
    {
        if(!$mysql_root = Args::get('mysql_root',Args::argv))
            die('You need to pass in mysql_root');

        mysql_connect('localhost','root',$mysql_root) or die(mysql_error());

        $dbname = $row['dbname'];
        $dbuser = $dbname;
        mysql_query('DROP DATABASE IF EXISTS `'.mysql_real_escape_string($dbname).'`');
        mysql_query('CREATE DATABASE `'.mysql_real_escape_string($dbname).'`') or die(mysql_error());
        mysql_query('GRANT ALL ON `'.mysql_real_escape_string($dbname).'`.* TO \''.mysql_real_escape_string($dbuser).'\'@\'localhost\' IDENTIFIED BY \''.mysql_real_escape_string($dbuser).'\'') or die(mysql_error());
        mysql_select_db($dbname);
        //CREATE A TABLE WITH A BUNCH OF DIFFERENT FIELD TYPES
        
        mysql_query('
CREATE TABLE `type_test` (
  `int_auto_field` int(10) NOT NULL AUTO_INCREMENT,
  `varchar_field_default_null` varchar(200) DEFAULT NULL,
  `varchar_field_default_something` varchar(200) NOT NULL DEFAULT \'something\',
  `int_field_default_10` int(10) NOT NULL DEFAULT \'10\',
  `int_field_default_null` int(10) DEFAULT NULL,
  `float_field_default_null` float DEFAULT NULL,
  `float_field_default_2point5` float NOT NULL DEFAULT 2.5,
  `double_field_default_null` double DEFAULT NULL,
  `double_field_default_2point555` double NOT NULL DEFAULT 2.555,
  `decimal_field_default_null` decimal(10,2) DEFAULT NULL,
  `decimal_field_default_10point2` decimal(10,2) NOT NULL DEFAULT 10.2,
  `datetime_field_default_null` datetime DEFAULT NULL,
  `datetime_field_default_something` datetime NOT NULL DEFAULT \'2012-01-01\',
  PRIMARY KEY(int_auto_field)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

    });
