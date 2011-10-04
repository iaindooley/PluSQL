<?php
    /**
    * dbname 
    * plusql 
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
        //create tables with composite keys so that:
        //
        //strong_guy -supports-> weak_guy
        //weak_guy   -has foreign-> french_guy
        //weak_guy   -many to many-> rogue_guy (with a joining table is_rogue)
        
        mysql_query('
CREATE TABLE `strong_guy` (
  `strong_guy_id` int(10) NOT NULL AUTO_INCREMENT,
  `strong_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY(strong_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

        mysql_query('
CREATE TABLE `weak_guy` (
  `strong_guy_id` int(10) NOT NULL DEFAULT \'0\',
  `weak_guy_id` int(10) NOT NULL AUTO_INCREMENT,
  `weak_name` varchar(20) DEFAULT NULL,
  `french_guy_id` int(10) NOT NULL DEFAULT \'0\',
  PRIMARY KEY(strong_guy_id,weak_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

        mysql_query('
CREATE TABLE `french_guy` (
  `french_guy_id` int(10) NOT NULL AUTO_INCREMENT,
  `french_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY(french_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

        mysql_query('
CREATE TABLE `rogue_guy` (
  `rogue_guy_id` int(10) NOT NULL AUTO_INCREMENT,
  `rogue_name` varchar(20) DEFAULT NULL,
  PRIMARY KEY(rogue_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

        mysql_query('
CREATE TABLE `is_rogue` (
  `strong_guy_id` int(10) NOT NULL DEFAULT \'0\',
  `weak_guy_id` int(10) NOT NULL DEFAULT \'0\',
  `rogue_guy_id` int(10) NOT NULL DEFAULT \'0\',
  PRIMARY KEY(strong_guy_id,weak_guy_id,rogue_guy_id)
) ENGINE=MyISAM DEFAULT CHARSET=latin1') or die(mysql_error());

        mysql_query('INSERT INTO strong_guy(strong_name) VALUES(\'Strongy Strongo\')');
        $strong_guy_id = mysql_insert_id();
        mysql_query('INSERT INTO french_guy(french_name) VALUES(\'Franco Phone\')');
        $french_guy_id = mysql_insert_id();
        mysql_query('INSERT INTO weak_guy(strong_guy_id,weak_name,french_guy_id) VALUES('.(int)$strong_guy_id.',\'Weaky Weakling\','.(int)$french_guy_id.')');
        $weak_guy_id = mysql_insert_id();
        mysql_query('INSERT INTO rogue_guy(rogue_name) VALUES(\'John McEnroe\','.(int)$french_guy_id.')');
        $rogue_guy_id = mysql_insert_id();
        mysql_query('INSERT INTO is_rogue(strong_guy_id,weak_guy_id,rogue_guy_id) VALUES('.$strong_guy_id.','.$weak_guy_id.','.$rogue_guy_id.')');
    });
