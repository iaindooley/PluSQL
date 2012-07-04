<?php
    namespace PluSQL;

    /**
    * This file provides a default autoload implementation for those not using https://github.com/iaindooley/RocketSled
    */
    spl_autoload_register(function($class)
    {
        //IF THIS IS A NAMESPACED CLASS AND IT'S PART OF plusql
        if((strpos($class,'\\') !== FALSE) && (strpos($class,'PluSQL') === 0))
        {
            $split = explode('\\',$class);
            array_shift($split);
            $file = classToFile(array_pop($split));
            $filename = dirname(__FILE__).'/'.implode('/',$split).'/'.$file;
        }

        else
            $filename = dirname(__FILE__).'/'.classToFile($class);
        
        if(file_exists($filename))
            require_once($filename);
    });

    function classToFile($class)
    {
        return strtolower(ltrim(preg_replace('/([A-Z])/','_\1',$class),'_')).'.class.php';
    }
