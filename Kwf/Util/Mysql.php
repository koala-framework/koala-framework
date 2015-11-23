<?php
class Kwf_Util_Mysql
{
    /**
     * Gibt zurück, ob der aktuelle db user file rechte hat
     *
     * @return bool $file_right TRUE if File_priv is set for $mysqlUser, otherwise FALSE
     */
    public static function getFileRight()
    {
        return self::hasPrivilege('FILE');
    }

    public static function hasPrivilege($privilege)
    {
        $data = Kwf_Registry::get('db')->query("SHOW GRANTS FOR CURRENT_USER()")->fetchAll();
        if (!count($data)) {
            throw new Kwf_Exception("MySQL rights not found");
        }

        foreach ($data as $k => $v) {
            $rightString = current($v);
            if (strpos($rightString, 'ON *.*') !== false) {
                if (preg_match('/^GRANT ALL PRIVILEGES/i', $rightString)) {
                    return true;
                } else if (preg_match('/^GRANT .*,?\s?'.$privilege.',?.* ON /is', $rightString)) {
                    return true;
                } else {
                    return false;
                }
            }
        }
        return false;
    }
}
