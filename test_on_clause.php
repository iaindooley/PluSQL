<?php
    require('dbconfig.php');
    require('on_clause.class.php');
    require('table_inspector.class.php');
    require('table_inspector_worker.class.php');
    $link = mysql_connect(DBHOST,DBUSER,DBPASS);
    mysql_select_db('plusql');
    $on = new OnClause($link,'author','book');
    echo $on->toString().PHP_EOL;
    $on = new OnClause($link,'book','author');
    echo $on->toString().PHP_EOL;
    $on = new OnClause($link,'book','book_type');
    echo $on->toString().PHP_EOL;
    $on = new OnClause($link,'reader','reader_reviews_book');
    echo $on->toString().PHP_EOL;
    $on = new OnClause($link,'book','reader_reviews_book');
    echo $on->toString().PHP_EOL;

    try
    {
        $on = new OnClause($link,'reader','book');
        echo $on->toString().PHP_EOL;
        echo 'Why did we not get an exception thrown when doing the on clause for reader and book?'.PHP_EOL;
    }
    
    catch(ManyToManyJoinException $exc)
    {
        echo 'the joining table is: '.$exc->joiningTable()->name().PHP_EOL;
    }
