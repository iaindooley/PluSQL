<?php
    require('connect.php');
    mysql_select_db('plusql');
    mysql_query('TRUNCATE author') or die(mysql_error());
    mysql_query('TRUNCATE book') or die(mysql_error());
    mysql_query('TRUNCATE reader') or die(mysql_error());
    mysql_query('TRUNCATE reader_reviews_book') or die(mysql_error());
    mysql_query('TRUNCATE book_type') or die(mysql_error());

    for($i = 1;$i <= 100;$i++)
    {
        $author_id = createAuthor($i);
        $reader_id = createReader($i);
        createBooksAndReviews($author_id,$reader_id,$i,hardcoverId());
        createBooksAndReviews($author_id,$reader_id,$i,paperbackId());
    }

    function createAuthor($id)
    {
        mysql_query('INSERT INTO author(author_first_name,author_last_name)
                     VALUES(\'Author\',\'Number '.$id.'\')') or die('err1: '.mysql_error());
        return mysql_insert_id();
    }

    function createReader($id)
    {
        mysql_query('INSERT INTO reader(reader_first_name,reader_last_name)
                     VALUES(\'Reader\',\'Number '.$id.'\')') or die(mysql_error());
        return mysql_insert_id();
    }
    
    function createBooksAndReviews($author_id,$reader_id,$num_to_create,$type_id)
    {
        for($i = 1;$i <= $num_to_create;$i++)
        {
            mysql_query('INSERT INTO book(author_id,title,book_type_id)
                         VALUES('.$author_id.',\'Book for author: '.$author_id.' number '.$i.'\','.$type_id.')') or die(mysql_error());
            $book_id = mysql_insert_id();
            mysql_query('INSERT INTO reader_reviews_book(reader_id,author_id,book_id,review_date,review_content)
                         VALUES('.$reader_id.','.$author_id.','.$book_id.',now(),\'I liked book: '.$i.' by: '.$author_id.'\')') or die(mysql_error());
        }
    }
    
    function hardcoverId()
    {
        mysql_query('REPLACE INTO book_type(book_type_id,type_description)
                     VALUES(1,\'Hardcover\')') or die('err4: '.mysql_error());
        return 1;
    }

    function paperbackId()
    {
        mysql_query('REPLACE INTO book_type(book_type_id,type_description)
                     VALUES(2,\'Paperback\')') or die('err6: '.mysql_error());
        return 2;
    }

    mysql_select_db('plusql_dev');
    mysql_query('TRUNCATE author') or die(mysql_error());
    mysql_query('TRUNCATE book') or die(mysql_error());
    mysql_query('TRUNCATE reader') or die(mysql_error());
    mysql_query('TRUNCATE reader_reviews_book') or die(mysql_error());
    mysql_query('TRUNCATE book_type') or die(mysql_error());

    for($i = 1;$i <= 5;$i++)
    {
        $author_id = createAuthor($i);
        $reader_id = createReader($i);
        createBooksAndReviews($author_id,$reader_id,$i,hardcoverId());
        createBooksAndReviews($author_id,$reader_id,$i,paperbackId());
    }
