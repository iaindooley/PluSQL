# PluSQL: The ORM for SQL lovers

by: iain@workingsoftware.com.au

PluSQL is a non-ActiveRecord based ORM for people that know SQL and love it.

Although PluSQL is written for PHP 5.3 (and specifically for mysql/mysqli) I hope that people will provide non-ActiveRecord based ORM implementations in other languages/frameworks.

## Goals



-----------------------------

Plusql consists of two parts:

 - the bit that reads a query
 - the bit that builds a query

-----------------------------
Typical usage:
-----------------------------

Plusql::credentials('name',array('localhost','user','pass','db'));

Plusql::into('live')->table_name($_POST)->filter()-insert();

foreach(Plusql::from('live')->table_one
                            ->table_two->select('key1,key2,f1,f2')
                            ->where('fname = \''.mysql_real_escape_string($some_value).'\'')
                            ->run()->table_one as $t1)
{
    echo $t1->f1.':'.$t1->table_two->f2.PHP_EOL;
}

-----------------------------
TODO
-----------------------------

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
