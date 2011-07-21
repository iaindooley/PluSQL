<?php
    $start = microtime(true);
    require_once('anorm.class.php');
    require_once('connection.class.php');
    require_once('anorm_query.class.php');
    require_once('anorm_query_iterator.class.php');
    require_once('anorm_query_row.class.php');
    require_once('table_inspector.class.php');
    require_once('table_inspector_worker.class.php');
    Anorm::credentials('live',array('localhost','root','ROOTPASS','anorm'));
    Anorm::credentials('dev',array('localhost','root','ROOTPASS','anorm_dev'));

    printEverything();
    die('took: '.(microtime(true) - $start).' and used: '.memory_get_peak_usage(true)/1024/1024);

    function printEverything()
    {
        $sql = 'SELECT * FROM author INNER JOIN book USING(author_id)
                                     INNER JOIN book_type USING(book_type_id)
                                     INNER JOIN reader_reviews_book USING(author_id,book_id)
                                     INNER JOIN reader USING(reader_id)
                                     ORDER BY author_id,book_id,reader_id
                                     LIMIT 10000';

        foreach(Anorm::begin('live')->query($sql)->author as $auth)
        {
            echo $auth->author_first_name.' '.$auth->author_last_name.' wrote: '.PHP_EOL;
            
            foreach($auth->book as $book)
            {
                echo $book->title.' which is a: '.$book->book_type->type_description.PHP_EOL;
                echo 'this has been reviewed by the following people:'.PHP_EOL;
                
                foreach($book->reader as $reader)
                    echo $reader->reader_first_name.' '.$reader->reader_last_name.': '.$reader->reader_reviews_book->review_content.' ('.$reader->reader_reviews_book->review_date.')'.PHP_EOL;
            }
        }
        
        authorNames();
        Anorm::end();
    }
    
    function authorNames()
    {
        $sql = 'SELECT * FROM author INNER JOIN book USING(author_id)
                                     INNER JOIN book_type USING(book_type_id)
                                     INNER JOIN reader_reviews_book USING(author_id,book_id)
                                     INNER JOIN reader USING(reader_id)
                                     ORDER BY author_id,book_id,reader_id';
        foreach(Anorm::begin('dev')->query($sql)->author as $auth)
            echo $auth->author_first_name.' '.$auth->author_last_name.PHP_EOL;
        
        justBookOne();
        Anorm::end();
    }
    
    function justBookOne()
    {
        $book = Anorm::begin('live')->query('SELECT * FROM book INNER JOIN author USING(author_id) WHERE book_id = 1')->book;
        echo $book->title.' by: '.$book->author->author_first_name.' '.$book->author->author_last_name.PHP_EOL;
        Anorm::end();
    }
