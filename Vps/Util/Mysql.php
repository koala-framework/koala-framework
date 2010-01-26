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
}
