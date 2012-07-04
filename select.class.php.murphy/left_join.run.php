<?php
    namespace PluSQL;
    use Plusql;
    require(dirname(__FILE__).'/functions.php');

    /**
    * Test if we can do a left join
    */
    \Murphy\Test::add(function($runner)
    {
        $sel   = new Select(getConnection());
        $query = (string)$sel->strong_guy
                             ->weak_guy->joinType(Table::LEFT_JOIN)
                             ->rogue_guy('weak_guy')->joinType(Table::LEFT_JOIN)
                             ->french_guy->joinType(Table::LEFT_JOIN)
                             ->select('strong_guy.strong_name,weak_guy.weak_name,rogue_guy.rogue_name,french_guy.french_name')
                             ->where('strong_guy.strong_guy_id > 1');

        if($query == 'SELECT strong_guy.strong_name,weak_guy.weak_name,rogue_guy.rogue_name,french_guy.french_name FROM strong_guy LEFT JOIN weak_guy ON (strong_guy.strong_guy_id = weak_guy.strong_guy_id OR (weak_guy.strong_guy_id IS NULL)) LEFT JOIN is_rogue ON (weak_guy.strong_guy_id = is_rogue.strong_guy_id AND weak_guy.weak_guy_id = is_rogue.weak_guy_id OR (is_rogue.strong_guy_id IS NULL AND is_rogue.weak_guy_id IS NULL)) LEFT JOIN rogue_guy ON (is_rogue.rogue_guy_id = rogue_guy.rogue_guy_id OR (rogue_guy.rogue_guy_id IS NULL)) LEFT JOIN french_guy ON (weak_guy.french_guy_id = french_guy.french_guy_id OR (french_guy.french_guy_id IS NULL)) WHERE strong_guy.strong_guy_id > 1')
            $runner->pass();
        else
            $runner->fail('Left joins do not work');
    });
