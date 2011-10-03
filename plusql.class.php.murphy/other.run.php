<?php
    /**
    * Something that requires isolated state
    */
    murphy\Test::add(function()
    {
        echo 'This is completely separated from that other one, see?'.PHP_EOL;
        print_r($_POST);
    });
