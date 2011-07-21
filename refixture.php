#!/bin/sh
mysql -u root -p$1 < anorm.sql
php fixture.php
