<?php
    namespace plusql\fixture\query;

    /**
    * @database plusql
    * @tables strong_guy,weak_guy,rogue_guy,is_rogue,french_guy
    * strong_name | weak_name | rogue_name | french_name
    * Strong 1    | Weak 1   | Rogue 1   | French 1
    * Strong 1    | Weak 2   | Rogue 1   | French 1
    * Strong 2    | Weak 3   | Rogue 2   | French 2
    * Strong 2    | Weak 4   | Rogue 2   | French 2
    * Strong 2    | Weak 3   | Rogue 1   | French 2
    */
    \murphy\Fixture::add(function($row)
    {
        $strong_guy_id = createOrRetrieveGuy('strong_guy',array('strong_guy_id'),'strong_name',$row['strong_name']);
        $weak_guy_id   = createOrRetrieveGuy('weak_guy',array('strong_guy_id','weak_guy_id'),'weak_name',$row['weak_name'],array('strong_guy_id' => $strong_guy_id));
        $rogue_guy_id  = createOrRetrieveGuy('rogue_guy',array('rogue_guy_id'),'rogue_name',$row['rogue_name']);
        $french_guy_id = createOrRetrieveGuy('french_guy',array('french_guy_id'),'french_name',$row['french_name']);
        
        mysql_query('UPDATE weak_guy SET french_guy_id = '.$french_guy_id.' WHERE strong_guy_id = '.$strong_guy_id.' AND weak_guy_id = '.$weak_guy_id);
        mysql_query('REPLACE INTO is_rogue VALUES('.$strong_guy_id.','.$weak_guy_id.','.$rogue_guy_id.')');
    });

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
