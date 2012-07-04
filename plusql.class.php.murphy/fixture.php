<?php
    /**
    * database_name | dbuser
    * plusql        | plusql
    * plusql_dev    | plusql
    */
    Murphy\Fixture::add(function($row)
    {
        if(!$mysql_root = Args::get('mysql_root',Args::argv))
            die('You need to pass in mysql_root');

        mysql_connect('localhost','root',$mysql_root) or die(mysql_error());
        
        $dbname = $row['database_name'];
        $dbuser = $row['dbuser'];
        mysql_query('DROP DATABASE IF EXISTS `'.mysql_real_escape_string($dbname).'`');
        mysql_query('CREATE DATABASE `'.mysql_real_escape_string($dbname).'`') or die(mysql_error());
        mysql_query('GRANT ALL ON `'.mysql_real_escape_string($dbname).'`.* TO \''.mysql_real_escape_string($dbuser).'\'@\'localhost\' IDENTIFIED BY \''.mysql_real_escape_string($dbuser).'\'') or die(mysql_error());
        mysql_select_db($dbname);
        mysql_query('
CREATE TABLE `fixture_data` (
  `field_value` text DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1');
        mysql_query('INSERT INTO fixture_data VALUES(\''.mysql_real_escape_string($dbname).' fixture value\')');
    });
