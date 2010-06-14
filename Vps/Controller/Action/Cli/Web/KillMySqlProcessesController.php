<?php
class Vps_Controller_Action_Cli_Web_KillMySqlProcessesController extends Vps_Controller_Action
{
    public static function getHelp()
    {
        return "Kills MySql processes that are running more then 10 seconds";
    }
    public function indexAction()
    {
        exec('echo "SHOW PROCESSLIST" | mysql --xml', $out);
        $xml = simplexml_load_string(str_replace('xsi:nil="true"', '', implode("\n", $out)));
        foreach ($xml->row as $row) {
            $p = array();
            foreach ($row->field as $f) {
                $p[(string)$f['name']] = (string)$f;
            }
            if (/*$p['State'] == 'Locked'*/ $p['Time'] > 10) {
                $sql = "KILL $p[Id]";
                echo "$sql\n";
                system('echo "'.$sql.'" | mysql');
            }
        }
        exit;
    }
}
