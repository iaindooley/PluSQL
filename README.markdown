# PluSQL: The ORM for SQL lovers

by: iain@workingsoftware.com.au

PluSQL is a non-ActiveRecord based ORM for people that know SQL and love it.

Although PluSQL is written for PHP 5.3 (and specifically for mysql/mysqli) I hope that people will provide non-ActiveRecord based ORM implementations in other languages/frameworks.

## Goals

* To reduce the tediousness of writing SQL without abstracting it

* No boilerplate classes

* Automatic detection of relationships based on primary key names

* Encourage good database design rather than discourage it (ie. support composite keys, don't require an id field on joining tables)

* Provide the ability to iterate over any SQL query using objects

* Be very fast (as close as possible to hand writing iteration code)

* Have a very very small memory footprint

* Make it easy to connect to multiple databases

## PluSQL is not:

* A database abstraction layer

* Compatible with anything other than mysql/mysqli (this is because it requires buffered query sets)

* A wrapper for PDO

## Quick start

### Installation and configuration

If you are using https://github.com/iaindooley/RocketSled then simply place the plusql package in your packages directory.

If you are using PluSQL with another package or framework, you just need to require the autoload config file, eg:

```php
<?php
    require('plusql/autoload.config.php');
    //ALL CLASSES WILL NOW BE AVAILABLE
```

PluSQL can be configured to connect to multiple databases. To add a database configuration you use the credentials method:

```php
$config = array('localhost','username','password','database');
Plusql::credentials('connection name',$config);

$config = array('localhost','username','password','database2');
Plusql::credentials('connection name 2',$config);
```

These connection names are then used to connect as required. Note that the credentials() method does not open a connection to the database so it's safe to set all your credentials up at the start of each script execution without worrying about unecesesarily connecting to a bunch of databases.

## Basic usage: C.R.U.D

Each time you do something with Plusql you use one of your configured database credential profiles. In the examples below I'll just use a variable called ```$profile```. So for the examples below, we would have done this in order to setup a connection::

```php
$config = array('localhost','username','password','database');
Plusql::credentials('default',$config);
$profile = 'default';
```

### Create and Replace


To insert from a form $_POST:

```php
Plusql::into($profile)->table_name($_POST)->insert();
```

What happens is:

1. Any field names that are in table_name in the database will be extracted from $_POST - anything not relevant to table_name will be ignored. This is useful if you have a form that must write to more than one table.

2. The values are automatically quoted and escaped. The quotes are added according to the target data types in the database and all values are escaped with mysql/mysqli real_escape_string. This can be changed by passing in an anonymous function to the insert() method that accepts three arguments: 
    
    * ```$link```: a link to the database - either a mysql resource or mysqli object depending on what you have installed
    
    * ```$field```: an associative array as loaded from a MySQL DESCRIBE query

    * ```$value```: the value to be escaped

You can do multi-value inserts by passing in more than one array of values, and these can be "jagged". Any missing values that are used in one array but not another will just use the default provided by the database:

```php
$ins = Plusql::into($profile);
$some_constant = 1;
$names = array('name1','name2');

foreach($names as $name)
    $ins->table_name(array('constant' => $some_constant,'name' => $name));

//now let's get a little jagged ....
$values = array('other_field' => 'some value');
$ins->table_name($values);

//the first two records will use the default value for other_field provided in the database
//the third record will use the default value for constant and name provided in the database
$ins->insert();
```

In order to prevent the default filtering behaviour, eg. if you have already done your escaping and quoting somewhere else, a dummy implementation of the filter is provided:

```php
Plusql::into($profile)->table_name($escaped_values)->insert(Plusql::dummyFilter());
```

If you want to replace, just call replace() instead of insert():

```php
Plusql::into($profile)->table_name($_POST)->replace();
```

If you want to look at the SQL you can use:

```php
echo Plusql::into($profile)->table_name($_POST)->insertSql();
echo Plusql::into($profile)->table_name($_POST)->replaceSql();
echo Plusql::into($profile)->table_name($_POST)->insertSql(Plusql::dummyFilter());
```

You can call insertSql() and replaceSql() multiple times on the same object:

```php
$ins = Plusql::into($profile)->table_name($_POST);
echo $ins->insertSql();
echo $ins->insertSql(Plusql::dummyFilter());
$ins->insert();
$ins->replace();
```

## TODO

 * make from() against() into() and on() use default database credentials

 * come up with a good "mix in" style to cast the objects returned from the iterator
   to a new class for implementing custom functionality (that one would normally include
   as part of the "boilerplate" class

 * custom ON clause

 * custom joining table

 * modifiying the various components

 * simplfying the process of including all relevant primary keys based on from clause

 * simplfying the default order clause by primary keys to create contiguous blocks

 * pagination (including for joined queries)
