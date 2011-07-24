DROP DATABASE IF EXISTS plusql;
CREATE DATABASE plusql;
DROP DATABASE IF EXISTS plusql_dev;
CREATE DATABASE plusql_dev;

USE plusql
DROP TABLE IF EXISTS author;
CREATE TABLE `author` (
  `author_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_first_name` varchar(255) NOT NULL DEFAULT '',
  `author_last_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS book;
CREATE TABLE `book` (
  `author_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `book_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `book_type_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`author_id`,`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS reader;
CREATE TABLE `reader` (
  `reader_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reader_first_name` varchar(255) NOT NULL DEFAULT '',
  `reader_last_name` varchar(255) NOT NULL DEFAULT '',
  `book_type_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`reader_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS reader_reviews_book;
CREATE TABLE `reader_reviews_book` (
  `reader_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `author_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `book_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `review_date` datetime DEFAULT NULL,
  `review_content` text DEFAULT NULL,
  PRIMARY KEY (`reader_id`,`author_id`,`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS book_type;
CREATE TABLE `book_type` (
  `book_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`book_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

USE plusql_dev
DROP TABLE IF EXISTS author;
CREATE TABLE `author` (
  `author_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `author_first_name` varchar(255) NOT NULL DEFAULT '',
  `author_last_name` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`author_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS book;
CREATE TABLE `book` (
  `author_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `book_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `book_type_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`author_id`,`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS reader;
CREATE TABLE `reader` (
  `reader_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `reader_first_name` varchar(255) NOT NULL DEFAULT '',
  `reader_last_name` varchar(255) NOT NULL DEFAULT '',
  `book_type_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`reader_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS reader_reviews_book;
CREATE TABLE `reader_reviews_book` (
  `reader_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `author_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `book_id` bigint(20) unsigned NOT NULL DEFAULT 0,
  `review_date` datetime DEFAULT NULL,
  `review_content` text DEFAULT NULL,
  PRIMARY KEY (`reader_id`,`author_id`,`book_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

DROP TABLE IF EXISTS book_type;
CREATE TABLE `book_type` (
  `book_type_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `type_description` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`book_type_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
