<?php
//group skipGoOnline
//group reallySlow

/**
 * @group slow
 * @group User
 * @group UserServiceConn
 * @group UserBruteForce
 */
class Vps_User_ServiceConnTest extends PHPUnit_Framework_TestCase
{
    public function testBruteServiceConnection()
    {
        $debugOutput = false;
        $numProcesses = 10; //mind. 10 damit der test sinn macht, bei >50 läuft der server heiß

        $cmd = "php bootstrap.php test forward --controller=vps_user_service-conn";
        $descriptorspec = array(
            1 => array("pipe", "w"),
        );

        for ($i=0; $i < $numProcesses; $i++) {
            $p = array();
            $procs[] = proc_open("$cmd 2>&1", $descriptorspec, $p);
            $pipes[] = $p;
            if ($debugOutput) echo "starting process $i\n";
        }
        $allOut = array();
        do {
            $allDone = true;
            foreach ($pipes as $i=>$p) {
                if (!feof($p[1])) {
                    $allDone = false;
                    $out = fgets($p[1], 2);
                    if ($out != '.' && $out != ':') {
                        $out .= fgets($p[1]);
                    }
                    if ($debugOutput) echo "process $i outputed with '$out'\n";
                    if (!isset($allOut[$i])) $allOut[$i] = '';
                    $allOut[$i] .= $out;
                }
            }
        } while (!$allDone);
        $failed = false;
        foreach ($procs as $i=>$p) {
            $ret = proc_close($p);
            if ($debugOutput) echo "process $i returned with $ret\n";
            if ($ret != 0) $failed = true;
        }
        if ($debugOutput) echo "\n";
        foreach ($allOut as $i=>$out) {
            if ($debugOutput) echo "output process $i:\n";
            if ($debugOutput) echo $out."\n\n";
        }
        if ($failed) {
            $this->fail("alt least one process failed:".print_r($allOut, true));
        }
    }
}
