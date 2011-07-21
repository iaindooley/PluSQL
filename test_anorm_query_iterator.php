<?php
    require_once('query_iterator.class.php');
    require_once('query.class.php');
    require_once('query_row.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    $link = mysql_connect('localhost','root','++WEREWOLFbatMITZVAH++');
    mysql_select_db('anorm');
    $query = new AnormQuery('SELECT * FROM book',$link);
    $iterator = new AnormQueryIterator($query,'book');
    
    echo 'the current row is: '.$iterator->current()->title.PHP_EOL;
    echo 'the next row is: '.$iterator->next()->title.PHP_EOL;
    $iterator->rewind();
    echo 'we rewound now the current row is: '.$iterator->current()->title.PHP_EOL;
    echo 'we just want to fetch a single field: '.$iterator->title.PHP_EOL;
    $iterator->rewind();
    echo 'we just rewound now let\'s loop through the whole thing'.PHP_EOL;
    
    foreach($iterator as $book)
        echo $book->title.PHP_EOL;

    echo 'okay done .. valid? '.$iterator->valid();
    $iterator->rewind();
    echo 'just rewound ... valid now? '.$iterator->valid();
    
    try
    {
        $author = $iterator->author;
        echo 'we were able to get an author iterator'.PHP_EOL;
    }
    
    catch(Exception $exc)
    {
        echo PHP_EOL.'why the hell weren\'t we able to get an author iterator? '.PHP_EOL;
        die($exc->getMessage());
    }
    
    try
    {
        echo 'the author is: '.$author->author_first_name.PHP_EOL;
    }
    
    catch(InvalidAnormQueryRowException $exc)
    {
        echo 'naturally, we can\'t query author information - there\'s no author table in the query'.PHP_EOL;
    }
    
    echo 'book was written by author id: '.$author->author_id.PHP_EOL;
    
    try
    {
        echo 'why the hell are we able to tell the author\'s name though: '.$author->author_first_name.PHP_EOL;
    }
    
    catch(InvalidAnormQueryRowException $exc)
    {
        echo 'we quite rightly cannot tell the author\'s name though'.PHP_EOL;
    }
    
    echo 'now with a different query we can loop through by book and print the author\'s name'.PHP_EOL;
    
    $query = new AnormQuery('SELECT * FROM book INNER JOIN author USING(author_id) ORDER BY book_id',$link);
    $book = new AnormQueryIterator($query,'book');

    foreach($book as $b)
        echo $b->title.' was written by '.$b->author->author_first_name.' '.$b->author->author_last_name.PHP_EOL;

    echo 'and now we can loop through by author and print a list of books'.PHP_EOL;

    $query = new AnormQuery('SELECT * FROM author INNER JOIN book USING(author_id) INNER JOIN book_type USING(book_type_id) ORDER BY author_id,book_id',$link);
    $author = new AnormQueryIterator($query,'author');

    foreach($author as $auth)
    {
        echo $auth->author_first_name.' '.$auth->author_last_name.' wrote the following books:'.PHP_EOL;
        
        foreach($auth->book as $book)
            echo $book->title.' ('.$book->book_type->type_description.')'.PHP_EOL;
    }
