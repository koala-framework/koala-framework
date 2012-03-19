<?php
/**
 * @group Util
 */
class Kwf_Util_Mysql_Test extends Kwf_Test_TestCase
{
    /* Test disabled because it requires a mysql user with GRANT permissions
    public function testGrantFileRight()
    {
        // einen test user erstellen
        $dbConfig = Kwf_Registry::get('db')->getConfig();
        $dbUser = 'test'.time();

        $sql = "GRANT ALL PRIVILEGES ON `".$dbConfig["dbname"]."` . * TO '$dbUser'@'localhost'; ";
        $cmd = "mysql -e ".escapeshellarg($sql);
        system($cmd, $ret);
        if ($ret) {
            throw new Kwf_ClientException("Konnte berechtigungen nicht setzen");
        }

        // checken, ob das recht FILE auf false gesetzt ist
        $sql = "SELECT `Host`, `User`, `File_priv` FROM `user` WHERE `User` = '".$dbUser."'";
        $cmd = "mysql mysql -e ".escapeshellarg($sql);
        exec($cmd, $output, $ret);
        if ($ret) {
            throw new Kwf_Exception("MySQL User konnte nicht gefunden werden.");
        }

        foreach ($output as $outputRow) {
            $sqlArray = explode("\t", $outputRow);
            if ($sqlArray[0] == 'Host' && $sqlArray[1] == 'User' && $sqlArray[2] == 'File_priv') {
                continue;
            }
            $this->assertEquals('N', $sqlArray[2]);
        }

        Kwf_Util_Mysql::grantFileRight($dbUser);

        // checken, ob das recht FILE auf true gesetzt ist
        unset($output);
        $sql = "SELECT `Host`, `User`, `File_priv` FROM `user` WHERE `User` = '".$dbUser."'";
        $cmd = "mysql mysql -e ".escapeshellarg($sql);
        exec($cmd, $output, $ret);
        if ($ret) {
            throw new Kwf_Exception("MySQL User konnte nicht gefunden werden.");
        }

        foreach ($output as $outputRow) {
            $sqlArray = explode("\t", $outputRow);
            if ($sqlArray[0] == 'Host' && $sqlArray[1] == 'User' && $sqlArray[2] == 'File_priv') {
                continue;
            }
            $this->assertEquals('Y', $sqlArray[2]);
        }

        // user löschen
        $sql = "DROP USER '$dbUser'@'localhost'; ";
        $cmd = "mysql -e ".escapeshellarg($sql);
        system($cmd, $ret);
        if ($ret) {
            throw new Kwf_ClientException("Konnte berechtigungen nicht setzen");
        }
    }
    */
}
