<?php
/**
 * @group skipGoOnline
 * @group slow
 * @group reallySlow
 * @group User
 * @group UserBruteForce 
 */
class Vps_User_BruteForceTest extends PHPUnit_Framework_TestCase
{
    /*
    deaktiviert, funktioniert manchmal ned korrekt
    public function testCreateManyAndSync()
    {
        $debugOutput = false;
        $numProcesses = 10; //mind. 10 damit der test sinn macht, bei >50 l�uft der server hei�

        $cmd = "php bootstrap.php test forward --controller=vps_user_brute-force-insert";
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
            $this->fail("alt least one process failed");
        }
    }

    public function testCreateOneMultipleTimes()
    {
        $debugOutput = false;
        $numProcesses = 10; //mind. 10 damit der test sinn macht, bei >50 l�uft der server hei�

        $prefix = uniqid('usr');
        $cmd = "php bootstrap.php test forward --controller=vps_user_brute-force-insert --action=create-one-user  --prefix=$prefix";
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
            $this->fail("alt least one process failed");
        }

        $createdId = false;
        foreach ($allOut as $i=>$out) {
            $out = trim($out, ':');
            if (!is_numeric($out)) {
                $this->fail("Non numeric output from process $i, should output created user id: $out");
            } else {
                $out = (int)$out;
                if ($out && $createdId) {
                    $this->fail("More than one user created");
                } else if ($out) {
                    $createdId = $out;
                }
            }
        }
        if (!$createdId) {
            $this->fail("No user created");
        }
    }
    */
}
