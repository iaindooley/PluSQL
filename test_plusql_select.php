<?php
    require('dbconfig.php');
    require_once('plusql.class.php');
    require_once('plusql_select.class.php');
    require_once('plusql_table.class.php');
    require_once('connection.class.php');
    require_once('on_clause.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    Plusql::credentials('live',array(DBHOST,DBUSER,DBPASS,'plusql'));
    echo Plusql::from('live')->author
                             ->book
                             ->reader('book')
                             ->book_type
                             ->_();
