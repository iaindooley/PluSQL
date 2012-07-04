<?php
    namespace PluSQL\fixture\query_iterator;
    require_once(dirname(__FILE__).'/common.php');

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
    \Murphy\Fixture::add(function($row)
    {
        $strong_guy_id = createOrRetrieveGuy('strong_guy',array('strong_guy_id'),'strong_name',$row['strong_name']);
        $weak_guy_id   = createOrRetrieveGuy('weak_guy',array('strong_guy_id','weak_guy_id'),'weak_name',$row['weak_name'],array('strong_guy_id' => $strong_guy_id));
        $rogue_guy_id  = createOrRetrieveGuy('rogue_guy',array('rogue_guy_id'),'rogue_name',$row['rogue_name']);
        $french_guy_id = createOrRetrieveGuy('french_guy',array('french_guy_id'),'french_name',$row['french_name']);
        
        mysql_query('UPDATE weak_guy SET french_guy_id = '.$french_guy_id.' WHERE strong_guy_id = '.$strong_guy_id.' AND weak_guy_id = '.$weak_guy_id);
        mysql_query('REPLACE INTO is_rogue VALUES('.$strong_guy_id.','.$weak_guy_id.','.$rogue_guy_id.')');
    });
