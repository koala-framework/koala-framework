<?php
//das ist notwendig damit cli scripte was mit dem apc cache machen können
//direkt in der cli ist das leider nicht möglich, da der speicher im webserver liegt
class Kwf_Util_Apc
{
    public static function getHttpPassword()
    {
        $file = 'cache/apcutilspass';
        if (!file_exists($file)) {
            file_put_contents($file, time().rand(100000, 1000000));
        }
        return file_get_contents($file);
    }

    public static function callClearCacheByCli($params)
    {
        $config = Kwf_Registry::get('config');
        $d = $config->server->domain;
        if (!$d && file_exists('cache/lastdomain')) {
            //this file gets written in Kwf_Setup to make it "just work"
            $d = file_get_contents('cache/lastdomain');
        }
        $s = microtime(true);
        $pwd = Kwf_Util_Apc::getHttpPassword();
        $urlPart = "http".($config->server->https?'s':'')."://apcutils:".Kwf_Util_Apc::getHttpPassword()."@";
        $url = "$urlPart$d/kwf/util/apc/clear-cache";

        $client = new Zend_Http_Client();
        $client->setMethod(Zend_Http_Client::POST);
        $client->setParameterPost($params);
        $client->setConfig(array(
            'timeout' => 60,
            'keepalive' => true
        ));

        $client->setUri($url);
        $body = 'could not reach web per http';
        try {
            $response = $client->request();
            $result = !$response->isError() && substr($response->getBody(), 0, 2) == 'OK';
            $body = $response->getBody();
        } catch (Exception $e) {
            $result = false;
        }
        if (!$result && $config->server->noRedirectPattern) {
            $d = str_replace(array('^', '\\', '$'), '', $config->server->noRedirectPattern);
            $url2 = "$urlPart$d/kwf/util/apc/clear-cache";
            try {
                $client->setUri($url2);
                $client->setParameterPost($params);
                $response = $client->request();
                $result = !$response->isError() && substr($response->getBody(), 0, 2) == 'OK';
                $body = $response->getBody();
            } catch (Exception $e) {
                $result = false;
            }
        }
        return array(
            'result' => $result,
            'message' => $body,
            'time' => round((microtime(true)-$s)*1000),
            'url' => $url,
            'url2' => isset($url2) ? $url2 : null,
            'params' => $params
        );
    }

    public static function dispatchUtils()
    {
        if (empty($_SERVER['PHP_AUTH_USER']) ||
            empty($_SERVER['PHP_AUTH_PW']) ||
            $_SERVER['PHP_AUTH_USER']!='apcutils' ||
            $_SERVER['PHP_AUTH_PW']!=self::getHttpPassword())
        {
            header('WWW-Authenticate: Basic realm="APC Utils"');
            throw new Kwf_Exception_AccessDenied();
        }

        if (substr($_SERVER['REQUEST_URI'], 0, 25) == '/kwf/util/apc/clear-cache') {
            $s = microtime(true);
            if (isset($_REQUEST['cacheSimple'])) {
                foreach ($_REQUEST['cacheSimple'] as $id) {
                    Kwf_Cache_Simple::clear($id);
                }
            }
            if (isset($_REQUEST['cacheIds'])) {
                foreach (explode(',', $_REQUEST['cacheIds']) as $cacheId) {
                    apc_delete($cacheId);
                }
            }
            if (isset($_REQUEST['files'])) {
                foreach (explode(',', $_REQUEST['files']) as $file) {
                    if (extension_loaded('Zend OPcache')) {
                        opcache_invalidate($file);
                    } else {
                        @apc_delete_file($file);
                    }
                }
            } else if (isset($_REQUEST['type']) && $_REQUEST['type'] == 'user') {
                apc_clear_cache('user');
            } else {
                if (extension_loaded('Zend OPcache')) {
                    opcache_reset();
                } else {
                    apc_clear_cache('file');
                }
            }
            echo 'OK '.round((microtime(true)-$s)*1000).' ms';
            exit;
        } else if ($_SERVER['REQUEST_URI'] == '/kwf/util/apc/get-counter-value') {
            $prefix = Kwf_Cache::getUniquePrefix().'bench-';
            echo apc_fetch($prefix.$this->_getParam('name'));
            exit;
        } else if ($_SERVER['REQUEST_URI'] == '/kwf/util/apc/stats') {
            self::stats();
        } else if ($_SERVER['REQUEST_URI'] == '/kwf/util/apc/iterate') {
            self::iterate();
        }
        throw new Kwf_Exception_NotFound();
    }

    public static function stats()
    {
        header('Content-Type: text/plain; charset=utf-8');

        $mem = apc_sma_info(true);
        $memSize    = $mem['num_seg'] * $mem['seg_size'];
        $memAvailable= $mem['avail_mem'];

        echo "size: ".round($memSize/(1024*1024))." MB\n";
        echo "avail: ".round($memAvailable/(1024*1024))." MB\n\n";
        $prefix = Kwf_Cache::getUniquePrefix();

        $it = new APCIterator('user', '#^'.preg_quote($prefix).'#', APC_ITER_KEY);
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB size\n\n";
        $totalSize = $it->getTotalSize();

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'-cc-').'#', APC_ITER_KEY);
        echo "view cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'-procI-').'#', APC_ITER_KEY);
        echo "processInput cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'-url-').'#', APC_ITER_KEY);
        echo "url cache:\n";
        echo $it->getTotalCount()." entries\n";
        echo round($it->getTotalSize()/(1024*1024))." MB (".round(($it->getTotalSize()/$totalSize)*100)."%)\n\n";

        $it = new APCIterator('user', '#^'.preg_quote($prefix.'-config-').'#', APC_ITER_KEY);
        echo "config cache:\n";
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
