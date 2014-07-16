<?php
class Kwf_Util_Process
{
    public static function getRunningProcesses()
    {
        $ret = array();
        exec('ps ax -o pid,ppid,time,rss,args', $out);
        $processesByParent = array();
        foreach ($out as $o) {
            if (preg_match('#^\s*([0-9]+)\s+([0-9]+)\s+([0-9]*):([0-9]*):([0-9]*)\s+([0-9]+)\s+(.*)#', $o, $m)) {
                $pid = (int)$m[1];
                $ppid = (int)$m[2];
                $cmd = $m[7];
                if (getmypid() == $pid) continue;
                $ret[$pid] = array(
                    'pid' => $pid,
                    'ppid' => $ppid,
                    'cmd' => trim($cmd),
                    'time' => $m[3]*60*60 + $m[4]*60 + $m[5], //verbratene prozessor time
                    'prettyTime' => $m[3].':'.$m[4].':'.$m[5],
                    'memory' => (int)$m[6]*1024
                );
            }
        }
        return $ret;
    }

    public static function getRunningWebProcesses()
    {
        $ret = array();
        $processesByParent = array();
        $processes = self::getRunningProcesses();
        foreach ($processes as $p) {
            $pid = $p['pid'];
            $ppid = $p['ppid'];
            if (!isset($processesByParent[$ppid])) $processesByParent[$ppid] = array();
            $processesByParent[$ppid][] = $pid;
            $cmd = $p['cmd'];
            $cmd = explode(' ', $cmd);
            if (substr(trim($cmd[0]), -3) != 'php') continue;
            unset($cmd[0]);
            if (substr($cmd[1], -13) != 'bootstrap.php' && $cmd[1] != '/usr/local/bin/vps') continue;
            unset($cmd[1]);
            $cwd = explode(' ', trim(`pwdx $pid 2>/dev/null`));
            if (count($cwd) != 2) continue;
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
                'time' => $p['time'], //verbratene prozessor time
                'prettyTime' => $p['prettyTime'],
                'memory' => $p['memory'],
                'childPIds' => array()
            );
        }
        foreach ($ret as &$r) {
            $pid = $r['pid'];
            $r['childPIds'] = self::_getChildProcesses($r['pid'], $processesByParent);
        }
        return $ret;
    }

    private static function _getChildProcesses($pid, $processesByParent)
    {
        $ret = array();
        if (isset($processesByParent[$pid])) {
            $ret = $processesByParent[$pid];
            foreach ($processesByParent[$pid] as $ppid) {
                $ret = array_merge($ret, self::_getChildProcesses($ppid, $processesByParent));
            }
        }
        return $ret;
    }
}
