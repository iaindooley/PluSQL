<?php
    require_once('anorm_query_row.class.php');
    require_once('anorm_query.class.php');
    require_once('anorm_query_iterator.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    $link = mysql_connect('localhost','root','ROOTPASS');
    mysql_select_db('anorm');
    $query = new AnormQuery('SELECT * FROM author',$link);
    $row = new AnormQueryRow($query,'author',$query->nextRow());
    echo 'sig: '.$row->keySignature().PHP_EOL;
    echo 'get: '.$row->author_first_name.PHP_EOL;

    $query = new AnormQuery('SELECT * FROM book',$link);
    $row = new AnormQueryRow($query,'book',$query->nextRow());
    echo 'sig: '.$row->keySignature().PHP_EOL;
    
    try
    {
        echo 'get: '.$row->book_title.PHP_EOL;
    }
    
    catch(InvalidAnormQueryRowException $exc)
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
