<?php
    require_once('plusql.class.php');
    require_once('plusql_select.class.php');
    require_once('plusql_table.class.php');
    $link = mysql_connect('localhost','root','ROOTPASS');
    mysql_select_db('plusql');
    
    echo Plusql::from()->author
                       ->book
                       ->reader_reviews_book
                       ->book
                       ->book_type
                       ->_();
    ;
