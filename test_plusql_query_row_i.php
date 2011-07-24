<?php
    require_once('plusql_query_row.class.php');
    require_once('plusql_query.class.php');
    require_once('plusql_query_iterator.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    $link = new mysqli('localhost','root','ROOTPASS');
    $link->select_db('plusql');
    $query = new PlusqlQuery('SELECT * FROM author',$link);
    $row = new PlusqlQueryRow($query,'author',0);
    echo 'sig: '.$row->keySignature().PHP_EOL;
    echo 'get: '.$row->author_first_name.PHP_EOL;

    $query = new PlusqlQuery('SELECT * FROM book',$link);
    $row = new PlusqlQueryRow($query,'book',0);
    echo 'sig: '.$row->keySignature().PHP_EOL;
    
    try
    {
        echo 'get: '.$row->book_title.PHP_EOL;
    }
    
    catch(InvalidPlusqlQueryRowException $exc)
    {
        echo 'we quite rightly could not get book_title'.PHP_EOL;
    }

    echo 'but we can get just title: '.$row->title.PHP_EOL;
    
    try
    {
        $new_iterator = $row->author;
        echo 'we were rightly able to get an iterator for the author table'.PHP_EOL;
    }
    
    catch(Exception $exc)
    {
        echo 'why the hell did we get an excpetion when trying to get an iterator for the author table?'.PHP_EOL;
    }
