<?php
class Vps_Util_Process
{
    public static function getRunningWebProcesses()
    {
        $ret = array();
        exec('ps ax -o pid,ppid,time,args', $out);
        $processesByParent = array();
        foreach ($out as $o) {
            if (preg_match('#^\s*([0-9]+)\s+([0-9]+)\s+([0-9]*):([0-9]*):([0-9]*)\s+(.*)#', $o, $m)) {
                $pid = (int)$m[1];
                $ppid = (int)$m[2];
                if (!isset($processesByParent[$ppid])) $processesByParent[$ppid] = array();
                $processesByParent[$ppid][] = $pid;
                $cmd = $m[6];
                if (getmypid() == $pid) continue;
                $cmd = explode(' ', $cmd);
                if (substr(trim($cmd[0]), -3) != 'php') continue;
                unset($cmd[0]);
                if (substr($cmd[1], -13) != 'bootstrap.php' && $cmd[1] != '/usr/local/bin/vps') continue;
                unset($cmd[1]);
                $cwd = explode(' ', trim(`pwdx $pid`));
                if ($cwd[1] != getcwd()) continue;
                $cmdWithoutArgs= '';
                $args = '';
                foreach ($cmd as $i=>$c) {
                    if (substr($c, 0, 2)=='--') {
                        $args = implode(' ', $cmd);
                        break;
                    }
                    $cmdWithoutArgs .= $c.' ';
                    unset($cmd[$i]);
                }
                $ret[] = array(
                    'pid' => $pid,
                    'ppid' => $ppid,
                    'cmd' => trim($cmdWithoutArgs),
                    'args' => $args,
                    'time' => $m[3]*60*60 + $m[4]*60 + $m[5], //verbratene prozessor time
                    'prettyTime' => $m[3].':'.$m[4].':'.$m[5],
                    'childPIds' => array()
                );
            }
        }
        foreach ($ret as &$r) {
            if (isset($processesByParent[$r['pid']])) {
                $r['childPIds'] = $processesByParent[$r['pid']];
            }
        }
        return $ret;
    }
}
