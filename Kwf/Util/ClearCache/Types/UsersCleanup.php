<?php
class Kwf_Util_ClearCache_Types_UsersCleanup extends Kwf_Util_ClearCache_Types_Abstract
{
    protected function _refreshCache($options)
    {
        // alle zeilen löschen die zuviel sind in kwf_users
        // nötig für lokale tests
        $db = Kwf_Registry::get('db');
        $dbRes = $db->query('SELECT COUNT(*) `cache_users_count` FROM `cache_users`')->fetchAll();
        if ($dbRes[0]['cache_users_count'] >= 1) {
            $dbRes = $db->query('SELECT COUNT(*) `sort_out_count` FROM `kwf_users`
                    WHERE NOT (SELECT cache_users.id
                                FROM cache_users
                                WHERE cache_users.id = kwf_users.id
                                )'
            )->fetchAll();
            $db->query('DELETE FROM `kwf_users`
                    WHERE NOT (SELECT cache_users.id
                                FROM cache_users
                                WHERE cache_users.id = kwf_users.id
                                )'
            );
            return $dbRes[0]['sort_out_count']." rows cleared";
        } else {
            return "skipping: cache_users is empty";
        }
    }

    public function getTypeName()
    {
        return 'usersCleanup';
    }

    public function doesRefresh() { return true; }
    public function doesClear() { return false; }
}
