<?php
//group reallySlow

/**
 * @group slow
 * @group User
 * @group UserBruteForce
 * @group reallySlow
 */
class Vps_User_BruteForceTest extends Vps_Test_TestCase
{
    public function setUp()
    {
        parent::setUp();
        Vps_Test_SeparateDb::createSeparateTestDb(dirname(__FILE__).'/bootstrap.sql');
    }

    public function tearDown()
    {
        $this->assertFalse(Vps_User_Model::isLockedCreateUser());
        Vps_Test_SeparateDb::restoreTestDb();
        parent::tearDown();
    }

    /**
     * Wenn ein User erstellt wird kann passieren dass ein anderer php prozess
     * als der der ihn eigentlich erstellt hat diesen neuen user synct und so
     * er bereits in der Datenbank steht.
     * Workaround: LOCK (hatten wir mal)
     *             statt INSERT ein REPLACE verwenden (also syncen)  <<---- AKTUELLE LÖSUNG
     */
    public function testCreateManyAndSync()
    {
        $debugOutput = false;
        $numProcesses = 10; //mind. 10 damit der test sinn macht, bei >50 läuft der server heiß

        $cmd = "php bootstrap.php test forward --controller=vps_user_brute-force-insert --testDb=".Vps_Test_SeparateDb::getDbName();
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
            $this->fail("alt least one process failed; output was: ".implode("\n", $allOut));
        }
    }

    public function testCreateOneMultipleTimes()
    {
        $debugOutput = false;
        $numProcesses = 10; //mind. 10 damit der test sinn macht, bei >50 läuft der server heiß

        $prefix = uniqid('usr');
        $cmd = "php bootstrap.php test forward --controller=vps_user_brute-force-insert --action=create-one-user --prefix=$prefix --testDb=".Vps_Test_SeparateDb::getDbName();
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
            $this->fail("alt least one process failed; output was: ".implode("\n", $allOut));
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
}
