#!/bin/sh
mysql -u root -p$1 < plusql.sql
php fixture.php
