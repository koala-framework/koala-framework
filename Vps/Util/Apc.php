<?php
//das ist notwendig damit cli scripte was mit dem apc cache machen können
//direkt in der cli ist das leider nicht möglich, da der speicher im webserver liegt
class Vps_Util_Apc
{
    public static function getHttpPassword()
    {
        $file = 'application/cache/apcutilspass';
        if (!file_exists($file)) {
            file_put_contents($file, time().rand(100000, 1000000));
        }
        return file_get_contents($file);
    }

    public static function dispatchUtils()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER']!='apcutils' ||
            $_SERVER['PHP_AUTH_PW']!=self::getHttpPassword())
        {
            header('WWW-Authenticate: Basic realm="APC Utils"');
            throw new Vps_Exception_AccessDenied();
        }

        if (substr($_SERVER['REQUEST_URI'], 0, 25) == '/vps/util/apc/clear-cache') {
            $s = microtime(true);
            if ($_GET['type'] == 'user') {
                if (class_exists('APCIterator')) {
                    $prefix = Vps_Cache::getUniquePrefix();
                    apc_delete_file(new APCIterator('user', '#^'.preg_quote($prefix, '#').'#'));
                    apc_delete_file(new APCIterator('user', '#^config_[^/]+'.preg_quote(getcwd(), '#').'#'));
                } else {
                    apc_clear_cache('user');
                }
            } else {
                $paths = array(preg_quote(getcwd(), '#'));
                foreach (explode(PATH_SEPARATOR, get_include_path()) as $p) {
                    if (substr($p, 0, 1) == '/') {
                        $paths[] = preg_quote($p, '#');
                    }
                }
                if (class_exists('APCIterator')) {
                    apc_delete_file(new APCIterator('file', '#^'.'('.implode('|', $paths).')'.'#'));
                } else {
                    apc_clear_cache('file');
                }
            }
            echo 'OK '.round((microtime(true)-$s)*1000).' ms';
            exit;
        } else if ($_SERVER['REQUEST_URI'] == '/vps/util/apc/get-counter-value') {
            $prefix = Vps_Cache::getUniquePrefix().'bench-';
            echo apc_fetch($prefix.$this->_getParam('name'));
            exit;
        } else if ($_SERVER['REQUEST_URI'] == '/vps/util/apc/stats') {
            self::stats();
        } else if ($_SERVER['REQUEST_URI'] == '/vps/util/apc/iterate') {
            self::iterate();
        }
        throw new Vps_Exception_NotFound();
    }

    public static function stats()
    {
        header('Content-Type: text/plain; charset=utf-8');

        $mem = apc_sma_info(true);
        $memSize    = $mem['num_seg'] * $mem['seg_size'];
        $memAvailable= $mem['avail_mem'];

        echo "size: ".round($memSize/(1024*1024))." MB\n";
        echo "avail: ".round($memAvailable/(1024*1024))." MB\n\n";
        $prefix = Vps_Cache::getUniquePrefix();

        $it = new APCIterator('user', '#^'.preg_quote($prefix).'#', APC_ITER_KEY);
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB size\n\n";
        $totalSize = $it->getTotalSize();

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'-cc-').'#', APC_ITER_KEY);
        echo "view cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'procI-').'#', APC_ITER_KEY);
        echo "processInput cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'url-').'#', APC_ITER_KEY);
        echo "url cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $load = explode(' ', file_get_contents('/proc/loadavg'));
        echo "load: ".$load[0]."\n";
        exit;
    }

    public static function iterate()
    {
        header('Content-Type: text/plain; charset=utf-8');

        $requiredSpazi = 1024*1024*128; //128MB
        //$requiredSpazi = 1024*1024*300;

        $mem = apc_sma_info(true);
        $memSize    = $mem['num_seg'] * $mem['seg_size'];
        $memAvailable= $mem['avail_mem'];

        if ($memAvailable > $requiredSpazi) {
            die();
        }
        $start = microtime(true);
        ini_set('memory_limit', '256M');
        ini_set('display_errors', 'on');
        set_time_limit(90);

        echo "size: ".round($memSize/(1024*1024))." MB\n";
        echo "avail: ".round($memAvailable/(1024*1024))." MB\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";

        $accessTime = array();
        $it = new APCIterator('user', '#^[^-]+\\-[^-]+\\-(\\-cc|procI|url)-#', APC_ITER_KEY | APC_ITER_MEM_SIZE | APC_ITER_ATIME);
        echo "total count: ".$it->getTotalCount()."\n";
        echo "total hits: ".$it->getTotalHits()."\n";
        echo "total size: ".round($it->getTotalSize()/(1024*1024))." MB\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";
        $webs = array();
        foreach ($it as $i) {
            preg_match('#^([^-]+)\\-([^-]+)\\-+([a-zA-Z0-9]+)#', $i['key'], $m);
            $key = $m[1].'-'.$m[2];
            if (!isset($webs[$key])) $webs[$key] = 0;
            $webs[$key] += $i['mem_size'];

            $accessTime[] = time() - $i['access_time'];
        }
        echo "min lastAccess: ".min($accessTime)."\n";
        echo "max lastAccess: ".max($accessTime)."\n";
        echo "avg lastAccess: ".round(array_sum($accessTime)/count($accessTime))."\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";
        unset($accessTime);
        echo "\n";
        arsort($webs);
        foreach($webs as $web=>$items) {
            echo "$web: ".round(($items/$it->getTotalSize())*100)."%\n";
        }
        reset($webs);
        $web = key($webs);
        unset($it);
        unset($webs);
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";

        $it = new APCIterator('user', '#^'.preg_quote($web).'\\-(\\-cc|procI|url)-#', APC_ITER_KEY | APC_ITER_MEM_SIZE | APC_ITER_ATIME);
        echo "\n$web\n";
        echo "total count: ".$it->getTotalCount()."\n";
        echo "total hits: ".$it->getTotalHits()."\n";
        echo "total size: ".round($it->getTotalSize()/(1024*1024))." MB\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";

        $accessTime = array();
        $size = array();
        foreach ($it as $i) {
            $t = time() - $i['access_time'];
            $accessTime[$i['key']] = $t;
            $size[$i['key']] = $i['mem_size'];
        }
        unset($it);
        echo "min lastAccess: ".min($accessTime)."\n";
        echo "max lastAccess: ".max($accessTime)."\n";
        echo "avg lastAccess: ".round(array_sum($accessTime)/count($accessTime))."\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";
        flush();
        arsort($accessTime);
        $deletedBytes = 0;
        $deletedCount = 0;
        foreach ($accessTime as $key=>$t) {
            $deletedBytes += $size[$key];
            $memAvailable += $size[$key];
            //echo "delete $key (".round($deletedBytes/(1024*1024))."MB)\n";
            //flush();
            apc_delete($key);
            $deletedCount++;
            if ($memAvailable > $requiredSpazi+(1024*1024*2)) {
                break;
            }
        }
        echo "deleted $deletedCount entries\n";
        echo "deleted ".round($deletedBytes/1024)." KB\n";
        echo "\nduration: ".round((microtime(true)-$start), 3)." s\n";
        exit;
    }
}
