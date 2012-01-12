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

## Basic Usage 

Each time you do something with Plusql you use one of your configured database credential profiles. In the examples below I'll just use a variable called ```$profile```. So for the examples below, we would have done this in order to setup a connection::

```php
$config = array('localhost','username','password','database');
Plusql::credentials('default',$config);
$profile = 'default';
```
### Retrieving and iterating over data

PluSQL intuits your database structure based on primary key names. Let's take the following table structure for example. If we were to give a "textual" description of this system we'd say:

_We have 5 tables: strong_guy, weak_guy, french_guy, rogue_guy and is_rogue. weak_guy depends on strong_guy in a 1 to many dependent relationship. A weak_guy can be a type of french_guy in a 1 to many foreign relationship (foreign, french ... get it?). A weak_guy can be associated with any number of rogue_guys via the is_rogue table._

The table structure looks like this:

```mysql
TABLE: strong_guy;
+---------------+-------------+------+-----+---------+----------------+
| Field         | Type        | Null | Key | Default | Extra          |
+---------------+-------------+------+-----+---------+----------------+
| strong_guy_id | int(10)     | NO   | PRI | NULL    | auto_increment |
| strong_name   | varchar(20) | YES  |     | NULL    |                |
+---------------+-------------+------+-----+---------+----------------+

TABLE: weak_guy;
+---------------+-------------+------+-----+---------+----------------+
| Field         | Type        | Null | Key | Default | Extra          |
+---------------+-------------+------+-----+---------+----------------+
| strong_guy_id | int(10)     | NO   | PRI | 0       |                |
| weak_guy_id   | int(10)     | NO   | PRI | NULL    | auto_increment |
| weak_name     | varchar(20) | YES  |     | NULL    |                |
| french_guy_id | int(10)     | NO   |     | 0       |                |
+---------------+-------------+------+-----+---------+----------------+

TABLE: french_guy;
+---------------+-------------+------+-----+---------+----------------+
| Field         | Type        | Null | Key | Default | Extra          |
+---------------+-------------+------+-----+---------+----------------+
| french_guy_id | int(10)     | NO   | PRI | NULL    | auto_increment |
| french_name   | varchar(20) | YES  |     | NULL    |                |
+---------------+-------------+------+-----+---------+----------------+

TABLE: rogue_guy;
+--------------+-------------+------+-----+---------+----------------+
| Field        | Type        | Null | Key | Default | Extra          |
+--------------+-------------+------+-----+---------+----------------+
| rogue_guy_id | int(10)     | NO   | PRI | NULL    | auto_increment |
| rogue_name   | varchar(20) | YES  |     | NULL    |                |
+--------------+-------------+------+-----+---------+----------------+

TABLE: is_rogue
+---------------+---------+------+-----+---------+-------+
| Field         | Type    | Null | Key | Default | Extra |
+---------------+---------+------+-----+---------+-------+
| strong_guy_id | int(10) | NO   | PRI | 0       |       |
| weak_guy_id   | int(10) | NO   | PRI | 0       |       |
| rogue_guy_id  | int(10) | NO   | PRI | 0       |       |
+---------------+---------+------+-----+---------+-------+
```

So because of the way the primary keys on each table are named, PluSQL can tell how these all relate to each other. Let's look at some sample queries against the above structure.

PluSQL also provides an ```escape()``` method which gives you back an anonymous function which will escape using either mysql/mysqli_real_escape_string:

```php
//get an escape function
$f = Plusql::escape($profile);

echo Plusql::from($profile)->strong_guy
                           ->weak_guy->select('strong_guy_id,weak_guy_id,strong_name,weak_name')
                           ->where('strong_guy_id = '.$f($_GET['something']))
                          ->orderBy('strong_guy_id,weak_guy_id);
```

As you can see, you are responsible for your SELECT clauses, WHERE clauses, etc. but PluSQL dramatically reduces the task of writing joins.

Although there is some stuff we can do in future releases to _simplify_ this for _common use cases_, the goal is not to abstract away or prevent you from having to write SQL, just to make it more maintanable and productive.

This also automatically works for many-to-many relationships, so above we have the weak_guy and rogue_guy joined via is_rogue:

```php
echo Plusql::from($profile)->weak_guy->rogue_guy->select('*');
```

This is useful because if you started off, for example, with weak_guy and rogue_guy being one to many, you could change to a many-to-many relationshp in future without having to change your SQL.

Note that you can just echo the query and the ```__toString()``` method will build the query. You can also run it:

```php
Plusql::from($profile)->weak_guy->rogue_guy->select('*')->run();
```

You can access a single object/row without iterating:

```php
echo Plusql::from($profile)->weak_guy
                           ->rogue_guy
                           ->select('*')
                           ->run()
                           ->weak_guy->rogue_guy->rogue_name.PHP_EOL;
```

or you can iterate over the results, and you can nest your iterations:

```php
foreach(Plusql::from($profile)->weak_guy
                              ->rogue_guy
                              ->select('*')
                              ->run()->weak_guy as $wg)
    foreach($wg->rogue_guy as $rg)
        echo $wg->weak_name.':'.$rg->rogue_name.PHP_EOL;
```

If you need to cumulatively build a query, you can update any part of the clause, and you can traverse relationships without iterating:

```php
$query = Plusql::from($profile)->strong_guy->weak_guy;
//later ...
$query->rogue_guy->select('strong_guy_id,weak_guy_id,rogue_guy_id,rogue_name');
//later still ...
$sel = $query->select();
$sel->select($sel.',weak_name')->where('strong_guy_id = 1');
//later STILL ...
$where = $sel->where();

foreach($sel->where('('.$where.') AND rogue_guy_id = 1')->run()
                                                        ->strong_guy as $sg)
{
    //TRAVERSE WITHOUT ITERATING
    echo $sg->weak_guy->weak_name.':'.$sg->weak_guy
                                         ->rogue_guy->rogue_name.PHP_EOL;
    
    //NOW ITERATE
    foreach($sg->weak_guy->rogue_guy as $rg)
        echo $rg->rogue_name.PHP_EOL;
}
```

You can also access fields on joining tables which is useful for storing "date joined" or similar, for example that last line above could be:

```php
echo $rg->is_rogue->strong_guy_id.PHP_EOL;
```

If you're building a query where you need to join many tables to one table, for example our weak_guy is joined to both french_guy and rogue_guy, you pass the name of the table you wish to join to again as an argument:

```php
Plusql::from($profile)->strong_guy
                      ->weak_guy
                      //this will return the weak_guy table so we can join rogue_guy to it
                      ->french_guy('weak_guy')
                      ->rogue_guy
```

You can also do LEFT JOINS and it will automatically add an OR (primary_key IS NULL) to your ON clause:

```php
Plusql::from($profile)->strong_guy
                      ->weak_guy->joinType(Table::LEFT_JOIN)
```

You cannot currently do custom ON clauses.

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

### Update

The primary difference here is the method you use to start a query which is ```on()``` instead of ```into()```. In addition to that, you need to call ```where()``` to specify which rows to update. Although the field values are escaped the same way as for an insert/replace query, you need to manually quote/escape your where clause, but this can be done by obtaining a default escape function from Plusql:

```php
//grab the appropriate escape function - either mysql or mysqli depending on what is installed
$f = Plusql::escape($profile);

//now update table_name with the data from $_POST where field_name is a value from GET. Note I have
//manually quoted the value, but I'm using the anonymous function returned from Plusql::escape() to 
//escape the value
Plusql::on($profile)->table_name($_POST)->where('field_name = \''.$f($_GET['value']).'\'')->update();
```

Just as with the insert/replace queries, the ```update()``` method accepts an anonymous function that can be used to filter the data input array:

```php
$f = Plusql::escape($profile);
Plusql::on($profile)->table_name($_POST)
                    ->where('field_name = \''.$f($_GET['value']).'\'')
                    ->update(Plusql::dummyFilter());
```

If you don't call where() then you will get an Exception of type ```UnsafeUpdateException```. If you really want to update an entire table, use:

```php
Plusql::on($profile)->table_name($values)->where(Update::ENTIRE_TABLE)->update();
```

### Delete and other "raw" queries

You can pass any SQL you like into PluSQL using the ```against()``` and ```run()``` methods, and this is how delete queries are handled:

```php
Plusql::against($profile)->run($sql);
```

This will return the same type of query object that a normal select does, and you can iterate over the result sets in two ways.

Firstly, you can iterate over the result set in a "raw" fashion, just getting each row as you go - basically the same as doing ```mysql_fetch_assoc()``` or similar:

```php
$query = Plusql::against($profile)->run($sql);

while($row = $query->nextRow())
    echo $row['field_name'].PHP_EOL;
```

Alternatively you can iterate over it using the same object style as you do with a normal select query:

```php
foreach(Plusql::against($profile)->run($sql)->table_name as $tn)
    echo $tn->field_name.PHP_EOL;
```

As with other query types you can do your escaping when building SQL using the ```Plusql::escape()``` anonymous function.

## Running the Murphy tests

PluSQL comes with a bunch of tests written using https://github.com/iaindooley/Murphy which is an automated testing framework for https://github.com/iaindooley/RocketSled.

In order to run these tests:

1. Download RocketSled

2. Download Murphy and put the folder in your RocketSled packages directory

3. Download PluSQL and put the folder in your RocketSled packages directory

4. ```cd RocketSled && php index.php Murphy```

This will execute all tests. You can execute a subset by using the include directive:

```php index.php Murphy include="packages/plusql/insert"```

For more information on RocketSled and Murphy, check out the README files in those projects.

## TODO

 * make from() against() into() and on() use default database credentials

 * Automated escaping of where clauses for select/update

 * come up with a good "mix in" style to cast the objects returned from the iterator
   to a new class for implementing custom functionality (that one would normally include
   as part of the "boilerplate" class)

 * custom ON clause

 * custom joining table

 * simplfying the process of including all relevant primary keys based on from clause

 * simplfying the default order clause by primary keys to create contiguous blocks

 * pagination (including for joined queries)
