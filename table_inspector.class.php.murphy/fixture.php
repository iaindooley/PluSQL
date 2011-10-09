<?php
    /**
    * dbname
    * plusql_one
    * plusql_two
    */
    murphy\Fixture::add(function($row)
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

        mysql_query('
CREATE TABLE `strong_guy` (
  `strong_guy_id` int(10) NOT NULL AUTO_INCREMENT,
  `strong_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY(strong_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());
    });
