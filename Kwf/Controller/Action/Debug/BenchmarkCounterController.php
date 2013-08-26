<?php
class Kwf_Controller_Action_Debug_BenchmarkCounterController extends Kwf_Controller_Action
{
    public function indexAction()
    {
        $ok = false;
        foreach (Kwf_Config::getValueArray('debug.benchmarkCounterAccessIp') as $i) {
            if (substr($i, -1)=='*') {
                $i = substr($i, 0, -1);
                if (substr($_SERVER['REMOTE_ADDR'], 0, strlen($i)) == $i) {
                    $ok = true;
                }
            } else {
                if ($_SERVER['REMOTE_ADDR'] == $i) $ok = true;
            }
        }
        if (!$ok) {
            throw new Kwf_Exception_AccessDenied();
        }

        $names = array(
            'content-requests',
            'asset-requests',
            'media-requests',
            'admin-requests',
            'fullpage-hit',
            'fullpage-miss',
            'dbqueries',
            'render-hit',
            'render-miss',
            'render-noviewcache',
            'viewcache-mem',
            'viewcache-db',
            'viewcache-miss',
            'viewcache-delete-page',
            'viewcache-delete-component',
            'viewcache-delete-master',
            'viewcache-delete-partial',
            'viewcache-delete-componentLink',
            'viewcache-delete-fullPage',
        );
        $out = array();
        $load = @file_get_contents('/proc/loadavg');
        $load = explode(' ', $load);
        $out['load'] = (float)$load[0];
        foreach ($names as $name) {
            $out[$name] = Kwf_Benchmark_Counter::getInstance()->getValue($name);
        }
        echo json_encode($out);
        exit;
    }
}
