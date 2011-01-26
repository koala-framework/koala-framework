<?php
/**
 * @group Model
 * @group Model_MirrorCache
 * @group Model_MirrorCache_SlowSource
 * @group slow
 */
class Vps_Model_MirrorCache_SlowSource_Test extends Vps_Test_TestCase
{
    public function testRequests()
    {
        $m = Vps_Model_Abstract::getInstance('Vps_Model_MirrorCache_SlowSource_TestModel');
        $m->getProxyModel()->deleteRows(array());
        sleep(6); //damit sicher abgelaufen

        $numProcesses = 10;
        $debugOutput = false;

        $cmd = "php bootstrap.php test forward --controller=vps_model_mirror-cache_slow-source_test";
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
            $this->assertEquals(3, trim($out));
        }
        if ($failed) {
            $this->fail("alt least one process failed; output was: ".implode("\n", $allOut));
        }
    }

}
