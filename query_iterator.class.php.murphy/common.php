<?php
    namespace PluSQL\fixture\query_iterator;

    function createOrRetrieveGuy($table,$keys,$name_field,$name,$insert_keys = NULL)
    {
        $check = mysql_query('SELECT `'.implode('`,`',$keys).'` FROM `'.$table.'` WHERE '.$name_field.' = \''.mysql_real_escape_string($name).'\'') or die(mysql_error());
        
        if(!mysql_num_rows($check))
        {
            $fields = array();
            $values = array();
            
            if($insert_keys)
            {
                foreach($insert_keys as $key_name => $key_value)
                {
                    $fields[] = $key_name;
                    $values[] = $key_value;
                }
            }
            
            $fields[] = $name_field;
            $values[] = $name;
            mysql_query('INSERT INTO '.$table.'('.implode(',',$fields).') VALUES(\''.implode('\',\'',$values).'\')');
            $ret = mysql_insert_id();
        }
        
        else
        {
            $row = mysql_fetch_assoc($check);
            $ret = $row[end($keys)];
        }
        
        return $ret;
    }
