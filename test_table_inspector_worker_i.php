<?php
    require_once('table_inspector_worker.class.php');
    $link = new mysqli(DBHOST,DBUSER,DBPASS);
    $link->select_db('plusql');
    $worker = new TableInspectorWorker('author',$link);
    print_r($worker->primaryKeys());
    $worker = new TableInspectorWorker('book',$link);
    print_r($worker->primaryKeys());
    $worker = new TableInspectorWorker('reader_reviews_book',$link);
    print_r($worker->primaryKeys());
