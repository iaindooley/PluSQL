<?php
    require('dbconfig.php');
    require_once('plusql.class.php');
    require_once('plusql_query.class.php');
    require_once('plusql_query_iterator.class.php');
    require_once('plusql_query_row.class.php');
    require_once('plusql_select.class.php');
    require_once('plusql_table.class.php');
    require_once('connection.class.php');
    require_once('on_clause.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    Plusql::credentials('live',array(DBHOST,DBUSER,DBPASS,'plusql'));
    $sql = Plusql::from('live')->author
                               ->book
                               ->reader('book')
                               ->book_type
                               ->select('book.author_id,book.book_id,author_first_name')
                               ->where('book.book_id > 1')
                               ->groupBy('author.author_id')
                               ->having('author.author_id > 1')
                               ->orderBy('book.author_id,book.book_id')
                               ->limit('100')
                               ->_;

    foreach(Plusql::begin('live')->query($sql)->author as $auth)
        echo $auth->author_first_name.PHP_EOL;
