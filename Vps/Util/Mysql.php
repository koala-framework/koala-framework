<?php
class Vps_Util_Mysql
{
    /**
     * Versucht über den MySQL User aus der .my.cnf MySQL Rechte für
     * einen Benutzer zu setzen.
     *
     * @param string $mysqlUser Der MySQL User dem das Recht gesetzt werden soll
     * @return null
     */
    public static function grantFileRight($mysqlUser)
    {
        $sql = "SELECT `Host`, `User`, `File_priv` FROM `user` WHERE `User` = '".$mysqlUser."'";
        // ja, das ist korrekt dass da 2x mysql steht
        $cmd = "mysql mysql -e ".escapeshellarg($sql);
        exec($cmd, $output, $ret);
        if ($ret) {
            throw new Vps_Exception("MySQL User konnte nicht gefunden werden.");
        }
        $usersWithHost = array();
        foreach ($output as $outputRow) {
            $sqlArray = explode("\t", $outputRow);
            if ($sqlArray[0] == 'Host' && $sqlArray[1] == 'User' && $sqlArray[2] == 'File_priv') {
                continue;
            }

            if ($sqlArray[2] == 'N') {
                $usersWithHost[] = "'".$sqlArray[1]."'@'".$sqlArray[0]."'";
            }
        }

        foreach ($usersWithHost as $userWithHost) {
            $sql = "GRANT FILE ON *.* TO ".$userWithHost.";";
            $cmd = "mysql -e ".escapeshellarg($sql);
            exec($cmd, $output, $ret);
            if ($ret) {
                throw new Vps_Exception("FILE Berechtigungen in MySQL für $userWithHost konnten nicht gesetzt werden. CSV Import wird nicht funktionieren.");
            }
        }
    }

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
        $data = Vps_Registry::get('db')->query("SHOW GRANTS FOR CURRENT_USER()")->fetchAll();
        if (!count($data)) {
            throw new Vps_Exception("MySQL rights not found");
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
