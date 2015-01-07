<?php
class Kwf_Util_ClearCache_Watcher_FindSymlinksRecFilterIterator extends RecursiveFilterIterator
{

    public function accept()
    {
        if ($this->getFileName() == 'node_modules') return false;
        if ($this->getFileName() == 'cache') return false;
        if ($this->getFileName() == 'log') return false;
        if ($this->getFileName() == 'temp') return false;
        if ($this->getFileName() == '.git') return false;
        return true;
    }

}

class Kwf_Util_ClearCache_Watcher
{
    private static function _startsWith($str, $startWith)
    {
        if ($str == $startWith) return true;
        if (substr($str, 0, strlen($startWith)) == $startWith) return true;
        return false;
    }
    private static function _endsWith($str, $endsWith)
    {
        if ($str == $endsWith) return true;
        if (substr($str, -strlen($endsWith)) == $endsWith) return true;
        return false;
    }

    private static function _informDuckcast($cacheType)
    {
        if (Kwf_Config::getValue('debug.duckcast.host')) {
            echo "Inform Duckcast ...";

            try {
                file_get_contents(
                    'http://'.Kwf_Config::getValue('debug.duckcast.host')
                        .':'.Kwf_Config::getValue('debug.duckcast.port').'/watcher?cacheType='.$cacheType
                );
            } catch (Exception $e) {
                echo " [".$e->getMessage()."]\n";
                return;
            }
            echo " [ok]\n";
        }
    }

    public static function watch()
    {
        if (Kwf_Config::getValue('whileUpdatingShowMaintenancePage')) {
            throw new Kwf_Exception_Client("Disable whileUpdatingShowMaintenancePage in config to use clear-cache-watcher");
        }
        $bufferUsecs = 200*1000;

        $watchPaths = array();
        if (Kwf_Config::getValue('application.id') == 'kwf') {
            //for ccw in kwf itself (where cwd is tests subdir)
            $watchPaths[] = realpath(getcwd().'/..');
        } else {
            $watchPaths[] = getcwd();
        }

        if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {

            $ret = array();
            exec('ps ax -o pid,ppid,user,args', $out);
            $processesByParent = array();
            foreach ($out as $o) {
                if (preg_match('#^\s*([0-9]+)\s+([0-9]+)\s+([a-zA-Z0-9-_]+)\s+(.*)#', $o, $m)) {
                    $pid = (int)$m[1];
                    $ppid = (int)$m[2];
                    if (!isset($processesByParent[$ppid])) $processesByParent[$ppid] = array();
                    $processesByParent[$ppid][] = $pid;
                }
            }
            foreach ($out as $o) {
                if (preg_match('#^\s*([0-9]+)\s+([0-9]+)\s+([a-zA-Z0-9-_]+)\s+(.*)#', $o, $m)) {
                    $pid = (int)$m[1];
                    $cmd = $m[4];
                    $user = $m[3];
                    if (getmypid() == $pid) continue;
                    $cmd = explode(' ', $cmd);
                    if (substr(trim($cmd[0]), -3) != 'php') continue;
                    unset($cmd[0]);
                    if (substr($cmd[1], -13) != 'bootstrap.php' && $cmd[1] != '/usr/local/bin/vps') continue;
                    unset($cmd[1]);
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
                    if (substr(trim($cmdWithoutArgs), 0, 19)=='clear-cache-watcher') {
                        echo "there is already a clear-cache-watcher running for your user: ".trim(`pwdx $pid`)."\n";
                        echo "klling it...\n";
                        if (isset($processesByParent[$pid])) {
                            foreach ($processesByParent[$pid] as $i) {
                                posix_kill($i, SIGINT);
                            }
                        }
                        posix_kill($pid, SIGINT);
                        echo "\n";
                    }
                }
            }
        }

        //inotifywait doesn't recurse into symlinks
        //so we add all symlinks to $watchPaths
        foreach ($watchPaths as $p) {
            $fsi = new RecursiveIteratorIterator(
                new Kwf_Util_ClearCache_Watcher_FindSymlinksRecFilterIterator(
                    new RecursiveDirectoryIterator($p)
                ),
                RecursiveIteratorIterator::SELF_FIRST
            );
            foreach ($fsi as $fso) {
                if ($fso->isLink()) {
                    foreach ($watchPaths as $p2) {
                        if (substr($fso->__toString(), 0, strlen($p2)) == $p2) {
                            continue 2;
                        }
                    }
                    $watchPaths[] = $fso->__toString();
                }
            }
        }

        $exclude = array(
            '*magick*',
            '.nfs*',
            '/.git/*',
            '*.kate-swp',
            '~',
            '/cache/*',
            '/log/*',
            '/temp/*',
            '/data/index/*',
            '/benchmarklog',
            '/querylog',
            '/eventlog',
            '/build/*',
            '/Gruntfile.js'
        );
        $backend = null;
        $out = array();
        exec("watchmedo --version 2>&1", $out, $ret);
        if (!$ret) {
            foreach ($exclude as &$e) {
                $e = '*'.$e;
            }
            $cmd = "watchmedo log --recursive --ignore-directories ".
                " --ignore-patterns ".escapeshellarg(implode(';', $exclude)).
                ' '.implode(' ', $watchPaths);
            if (strtoupper(substr(PHP_OS, 0, 3)) !== 'WIN') {
                //disble output bufferering
                $cmd = "PYTHONUNBUFFERED=1 $cmd";
            } else {
                //on windows disable output buffering using -u
                //the above doesn't work
                $cmd = "python -u -m watchdog.$cmd";
            }
            $backend = 'watchmedo';
        } else {
            $out = array();
            $str = exec("inotifywait --help 2>&1", $out, $ret);
            if ($ret > 1 || substr($out[0], 0, 12) != 'inotifywait ') {
                if (stristr(PHP_OS, 'LINUX')) {
                    throw new Kwf_Exception_Client(
                        "To use clear-cache-watcher you need either inotifywait or watchmedo installed and in your \$PATH:\n".
                        "- install inotify-tools package to get inotifywait (see https://github.com/rvoicilas/inotify-tools/wiki)\n".
                        "- install python-watchdog (see http://pythonhosted.org/watchdog/#easy-installation)"
                    );
                } else {
                    throw new Kwf_Exception_Client(
                        "To use clear-cache-watcher you need watchmedo installed and in your \$PATH:\n".
                        "- install python-watchdog (see http://pythonhosted.org/watchdog/#easy-installation)"
                    );
                }
            }
            $excludeRegEx = array();
            foreach ($exclude as $e) {
                if (substr($e, -1) == '*') $e = substr($e, 0, -1); //not needed
                $excludeRegEx[] = str_replace(
                    array(
                        '.',
                        '*',
                    ),
                    array(
                        '\\.',
                        '.*'
                    ),
                    $e);
            }
            $excludeRegEx = implode('|', $excludeRegEx);
            $cmd = "inotifywait -e modify -e create -e delete -e move -e moved_to -e moved_from -r --monitor ".
                "--exclude '$excludeRegEx' ".
                implode(' ', $watchPaths);
            $backend = 'inotifywait';
        }
        echo $cmd."\n";

        require VENDOR_PATH.'/autoload.php';
        $proc = new Symfony\Component\Process\Process($cmd, null, null, null, null, array());
        $proc->start();

        $eventsQueue = array();
        $lastChange = false;
        while(true) {

            if ($lastChange && $lastChange+($bufferUsecs/1000000) < microtime(true)) {
                $eventsQueue = array_unique($eventsQueue);
                if (count($eventsQueue) > 100) {
                    echo "more than 100 events (".count($eventsQueue)."), did you switch branches or something?\n";
                    echo "I'm giving up.\n";
                    //TODO: clear-cache and restart clear-cache-watcher

                    $ppid = $proc->getPid();
                    //use ps to get all the children of this process, and kill them
                    $pids = preg_split('/\s+/', `ps -o pid --no-heading --ppid $ppid`);
                    foreach($pids as $pid) {
                        if(is_numeric($pid)) {
                            echo "Killing $pid\n";
                            posix_kill($pid, 9); //9 is the SIGKILL signal
                        }
                    }
                    $proc->terminate();
                    $proc->close(false);
                    exit;
                }
                if ($backend == 'inotifywait') {
                    foreach ($eventsQueue as $k=>$event) {
                        if (!preg_match('#^([^ ]+) ([A-Z,_]+) ([^ ]+)$#', trim($event), $m)) {
                            echo "unknown event: $event\n";
                            continue;
                        }
                        $eventsQueue[$k] = array(
                            'event' => $m[2],
                            'file' => $m[1].$m[3],
                        );
                        unset($m);
                    }

                    //inotifywait reports move a two individual events, compress into one MOVE
                    foreach ($eventsQueue as $k=>$event) {
                        if ($eventsQueue[$k]['event'] == 'MOVED_TO' && $k >= 1) {
                            $eventsQueue[$k]['event'] = 'MOVE';
                            if ($eventsQueue[$k-1]['event'] != 'MOVED_FROM') {
                                throw new Kwf_Exception('MOVED_FROM event is not followed by a MOVED_TO');
                            }
                            $eventsQueue[$k]['dest'] = $eventsQueue[$k]['file'];
                            $eventsQueue[$k]['file'] = $eventsQueue[$k-1]['file'];
                            unset($eventsQueue[$k-1]);
                        }
                    }
                    $eventsQueue = array_values($eventsQueue);

                } else if ($backend == 'watchmedo') {
                    foreach ($eventsQueue as $k=>$event) {
                        if (!preg_match('#^on_([a-z]+)\(.*event=.*src_path=u?\'([^\']+)\'(, dest_path=u?\'([^\']+)\')?#', trim($event), $m)) {
                            echo "unknown event: $event\n";
                            continue;
                        }
                        $ev = $m[1];
                        if ($ev == 'modified') $ev = 'MODIFY';
                        if ($ev == 'created') $ev = 'CREATE';
                        if ($ev == 'deleted') $ev = 'DELETE';
                        if ($ev == 'moved') $ev = 'MOVE';
                        $m[2] = str_replace('\\\\', '/', $m[2]); //windows
                        $eventsQueue[$k] = array(
                            'event' => $ev,
                            'file' => $m[2]
                        );
                        if ($ev == 'MOVE') {
                            $m[4] = str_replace('\\\\', '/', $m[4]);
                            $eventsQueue[$k]['dest'] = $m[4];
                        }
                        unset($m);
                    }
                }

                // compress the following into into one event:
                // CREATE web.scssdx1493.new
                // MODIFY web.scssdx1493.new
                // (or in other order, which can happen
                $eventsQueue = array_values($eventsQueue);
                foreach ($eventsQueue as $k=>$event) {
                    if (($event['event'] == 'MODIFY' || $event['event'] == 'CREATE') && $k >= 1) {
                        $f = $eventsQueue[$k]['file'];
                        if (($eventsQueue[$k-1]['event'] == 'CREATE' || $eventsQueue[$k-1]['event'] == 'MODIFY')
                            && substr($eventsQueue[$k-1]['file'], 0, strlen($f)) == $f
                        ) {
                            $eventsQueue[$k]['event'] = 'MODIFY';
                            unset($eventsQueue[$k-1]);
                        }
                    }
                }

                // compress the following into into one event:
                // MODIFY web.scssdx1493.new
                // MOVED web.scssdx1493.new web.scss
                $eventsQueue = array_values($eventsQueue);
                foreach ($eventsQueue as $k=>$event) {
                    if ($event['event'] == 'MOVE' && $k >= 1) {
                        $f = $eventsQueue[$k]['dest'];
                        if ($eventsQueue[$k-1]['event'] == 'MODIFY'
                            && substr($eventsQueue[$k]['file'], 0, strlen($f)) == $f
                            && substr($eventsQueue[$k-1]['file'], 0, strlen($f)) == $f
                        ) {
                            unset($eventsQueue[$k-1]);
                            $eventsQueue[$k]['event'] = 'MODIFY';
                            $eventsQueue[$k]['file'] = $f;
                            unset($eventsQueue[$k]['dest']);
                        }
                    }
                }
                foreach ($eventsQueue as $event) {
                    $eventStart = microtime(true);
                    self::_handleEvent($event['file'], $event['event']);
                    echo "finished in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
                }
                $eventsQueue = array();
                $lastChange = false;
            }

            $event = trim($proc->getIncrementalOutput());

            if (!$event) {
                usleep($bufferUsecs/2);
                continue;
            }
            $eventsQueue = array_merge($eventsQueue, explode("\n", $event));

            $lastChange = microtime(true);

        }
        $proc->close();
        exit;
    }

    private static function _handleEvent($file, $event)
    {
        echo "\n$event $file\n";
        Kwf_Cache_Simple::resetZendCache(); //reset to re-fetch namespace
        $eventStart = microtime(true);
        if (substr($file, -4)=='.css' || substr($file, -3)=='.js' || substr($file, -9)=='.printcss' || substr($file, -5)=='.scss') {
            echo "asset modified: $event $file\n";
            if ($event == 'MODIFY') {

                //there is no cache for individual files, scss cache is handled in Kwf_Assets_Dependency_File_Scss using mtime

                $assetsType = substr($file, strrpos($file, '.')+1);
                if ($assetsType == 'scss') $assetsType = 'css';
                self::_clearAssetsAll($assetsType);
                if ($assetsType == 'js') self::_clearAssetsAll('defer.js');

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";

            } else if ($event == 'CREATE' || $event == 'DELETE' || $event == 'MOVE') {

                self::_clearAssetsDependencies();

                $assetsType = substr($file, strrpos($file, '.')+1);
                if ($assetsType == 'scss') $assetsType = 'css';
                self::_clearAssetsAll($assetsType);
                if ($assetsType == 'js') self::_clearAssetsAll('defer.js');
                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
            }

        } else if (self::_endsWith($file, '/dependencies.ini')) {
            if ($event == 'MODIFY') {

                self::_clearAssetsDependencies();

                self::_clearAssetsAll();

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
            }
        } else if (preg_match('#/config([^/]*)?\.ini$#', $file)) { //config.ini, configPoi.ini, config.local.ini (in any directory)
            if ($event == 'MODIFY') {

                $section = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
                $cacheId = 'config_'.str_replace('-', '_', $section);
                Kwf_Config_Cache::getInstance()->remove($cacheId);
                echo "removed config cache\n";

                $apcCacheId = $cacheId.getcwd();
                $cacheIds = array();
                $cacheIds[] = $apcCacheId;
                $cacheIds[] = $apcCacheId.'mtime';

                self::_clearApcCache(array(
                    'cacheIds' => $cacheIds,
                    'clearCacheSimpleStatic' => array(
                        'config-',
                        'configAr-',
                    )
                ));
                echo "cleared apc config cache\n";

                $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php clear-cache --type=setup";
                exec($cmd, $out, $ret);
                echo "cleared setup.php cache";
                if ($ret) echo " [FAILED]";
                echo "\n";

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
            }
        } else if (preg_match('#(Acl|MenuConfig)\.php$#', $file)) {
            if ($event == 'MODIFY') {
                Kwf_Acl::clearCache();
                echo "cleared acl cache...\n";

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
            }
        } else if (self::_endsWith($file, '.twig')) {
            $loader = new Twig_Loader_Filesystem('.');
            $twig = new Twig_Environment($loader, array(
                'cache' => 'cache/twig'
            ));
            $cacheFile = $file;
            $cacheFile = substr($cacheFile, strlen(getcwd())+1);
            $cacheFile = $twig->getCacheFilename($cacheFile);

            if (file_exists($cacheFile)) {
                unlink($cacheFile);
                echo "cleared twig cache file '$cacheFile' for template '$file'\n";
            }
        }

        if (self::_startsWith($file, getcwd().'/components')
            || self::_startsWith($file, getcwd().'/theme')
            || self::_startsWith($file, KWF_PATH.'/Kwc')
            || (defined('VKWF_PATH') && self::_startsWith($file, VKWF_PATH.'/Vkwc'))
        ) {
            $cls = null;
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                if (substr($ip, 0, 1) == '.') $ip = getcwd().substr($ip, 1);
                if (substr($ip, 0, 1) != '/') $ip = getcwd().'/'.$ip;
                if (self::_startsWith($file, $ip)) {
                    $cls = str_replace('/', '_', substr($file, strlen($ip)+1, -4));
                }
            }

            if (!$cls) {
                echo "unknown component class?!\n";
                return;
            }
            if (!self::_endsWith($file, '/Component.php')) {
                //other component file
                $cls = substr($cls, 0, strrpos($cls, '_')).'_Component';
            }
            echo "component $cls\n";

            if (!@class_exists($cls)) {
                echo "parse error: $cls\n";
                return;
            }

            $matchingClasses = array();
            try {
                foreach (Kwc_Abstract::getComponentClasses() as $c) {
                    if (is_instance_of($c, $cls)) {
                        $matchingClasses[] = $c;
                    }
                }
            } catch (Exception $e) {}

            if (self::_endsWith($file, '/Admin.php') ||
                       self::_endsWith($file, '/Master.tpl') ||
                       self::_endsWith($file, '/Component.tpl') ||
                       self::_endsWith($file, '/Partial.tpl') ||
                       self::_endsWith($file, '/Master.twig') ||
                       self::_endsWith($file, '/Component.twig') ||
                       self::_endsWith($file, '/Partial.twig') ||
                       self::_endsWith($file, '/Controller.php') ||
                       self::_endsWith($file, '/FrontendForm.php') ||
                       self::_endsWith($file, '/Form.php') ||
                       self::_endsWith($file, '/Component.css') ||
                       self::_endsWith($file, '/Component.scss') ||
                       self::_endsWith($file, '/Component.printcss')
            ) {
                if ($event == 'CREATE' || $event == 'DELETE') {
                    echo "recalculate 'componentFiles' setting because comopnent file got removed/added...\n";
                    self::_clearComponentSettingsCache($matchingClasses, 'componentFiles');
                }
            }

            if (   self::_endsWith($file, '/Component.php') ||
                self::_endsWith($file, '/FrontendForm.php') //viewCache for forms is enabled
            ) {
                if ($event == 'MODIFY') {
                    self::_clearComponentSettingsCache($matchingClasses);

                    //view cache can depend on settings
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Component.css') || self::_endsWith($file, '/Component.scss') || self::_endsWith($file, '/Component.printcss')) {
                //MODIFY already handled above (assets)
                //CREATE/DELETE also handled above
            } else if (self::_endsWith($file, '/Master.tpl') || self::_endsWith($file, '/Master.twig')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    //all component_classes
                    $s->whereEquals('type', 'master');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Component.tpl') || self::_endsWith($file, '/Component.twig')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'component');
                    $s->whereEquals('renderer', 'component');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Partial.tpl') || self::_endsWith($file, '/Partial.twig')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'partial');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Mail.html.tpl') || self::_endsWith($file, '/Mail.html.twig')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'component');
                    $s->whereEquals('renderer', 'mail_html');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Mail.txt.tpl') || self::_endsWith($file, '/Mail.txt.twig')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'component');
                    $s->whereEquals('renderer', 'mail_txt');
                    self::_deleteViewCache($s);
                }
            }
            echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
        }
    }

    private static function _clearApcCache($params)
    {
        echo "APC: ";
        foreach ($params as $k=>$i) {
            if (is_array($i)) {
                $params[$k] = implode(',', $i);
            }
        }
        Kwf_Util_Apc::callClearCacheByCli($params, array('outputFn' => 'printf'));
    }

    private static function _getComponentClassesFromGeneratorsSetting($generators)
    {
        $ret = array();
        foreach ($generators as $gen) {
            $cmpClasses = $gen['component'];
            if (!is_array($cmpClasses)) $cmpClasses = array($cmpClasses);
            foreach ($cmpClasses as $cmpClass) {
                if ($cmpClass) $ret[] = $cmpClass;
            }
        }
        return array_unique($ret);
    }

    private static function _clearComponentSettingsCache($componentClasses, $setting = null)
    {
        Kwf_Component_Abstract::resetSettingsCache();

        $settings = Kwf_Component_Settings::_getSettingsCached();

        $dependenciesChanged = false;
        $generatorssChanged = false;
        $dimensionsChanged = false;
        $menuConfigChanged = false;
        foreach ($componentClasses as $c) {
            Kwf_Component_Settings::$_rebuildingSettings = true;
            if ($setting) {
                //a single setting changed
                $newSettings = $settings[$c];
                $newSettings[$setting] = Kwc_Abstract::getSetting($c, $setting);
            } else {
                //all settings might have changed
                $newSettings = Kwf_Component_Settings::_getSettingsIncludingPreComputed($c);
            }
            Kwf_Component_Settings::$_rebuildingSettings = false;

            if ($newSettings['assets'] != $settings[$c]['assets']
                || $newSettings['assetsAdmin'] != $settings[$c]['assetsAdmin']
            ) {
                $dependenciesChanged = true;
            }
            if ($newSettings['generators'] != $settings[$c]['generators']) {
                $generatorssChanged = true;
                $oldChildComponentClasses = self::_getComponentClassesFromGeneratorsSetting($settings[$c]['generators']);
                $newChildComponentClasses = self::_getComponentClassesFromGeneratorsSetting($newSettings['generators']);
            }
            if (isset($newSettings['dimensions']) && $newSettings['dimensions'] != $settings[$c]['dimensions']) {
                $dimensionsChanged = true;
            }
            if (isset($newSettings['menuConfig']) && $newSettings['menuConfig'] != $settings[$c]['menuConfig']) {
                $menuConfigChanged = true;
            }
            $settings[$c] = $newSettings;
        }

        echo "refreshed component settings cache...\n";

        if ($dependenciesChanged) {
            echo "assets changed...\n";
            self::_clearAssetsDependencies();
            self::_clearAssetsAll();
        }

        $clearCacheSimpleStatic = array(
            'has-', //Kwf_Component_Abstract::hasSetting
            'cs-', //Kwf_Component_Abstract::getSetting
            'flag-', //Kwf_Component_Abstract::getFlag
            'componentClasses-', //Kwf_Component_Abstract::getComponentClasses
            'recCCGen-', //Kwf_Component_Data::getRecursiveChildComponents
            'genInst-', //Kwf_Component_Generator_Abstract::getInstances
            'childComponentClasses-', //Kwf_Component_Generator_Abstract::getChildComponentClasses
        );

        if ($generatorssChanged) {
            echo "generators changed...\n";
            echo count(Kwc_Abstract::getComponentClasses())." component classes (previously)\n";

            $m = Kwf_Component_Cache::getInstance()->getModel('url');
            foreach ($m->getRows() as $r) {
                Kwf_Cache_Simple::delete('url-'.$r->url);
                $r->delete();
            }
            foreach ($newChildComponentClasses as $cmpClass) {
                if (!in_array($cmpClass, Kwc_Abstract::getComponentClasses())) {
                    self::_loadSettingsRecursive($settings, $cmpClass);
                }
            }
            $removedComponentClasses = array_diff($oldChildComponentClasses, $newChildComponentClasses);
            foreach ($removedComponentClasses as $removedCls) {
                self::_removeSettingsRecursive($settings, $removedCls);
            }
        }
        file_put_contents('build/component/settings', serialize($settings));

        echo "cleared component settings apc cache...\n";
        self::_clearApcCache(array(
            'clearCacheSimpleStatic' => $clearCacheSimpleStatic,
        ));
        echo "\n";

        if ($dimensionsChanged) {
            echo "dimensions changed...\n";
            $clearCacheSimple = array();
            foreach ($componentClasses as $c) {
                $idPrefix = str_replace(array('.', '>'), array('___', '____'), $c) . '_';
                $clearCacheSimple[] = 'media-output-'.$idPrefix;
                $clearCacheSimple[] = 'media-output-mtime-'.$idPrefix;
                foreach (glob('cache/media/'.$idPrefix.'*') as $f) {
                    echo $f." [DELETED]\n";
                    unlink($f);
                }
            }
            Kwf_Cache_Simple::delete($clearCacheSimple);
            echo "cleared media cache...\n";
        }
        if ($menuConfigChanged) {
            echo "menu config changed...\n";
            Kwf_Acl::clearCache();
            echo "cleared acl cache...\n";
        }

        $dependentComponentClasses = array();
        foreach (Kwc_Abstract::getComponentClasses() as $c) {
            if (strpos($c, '.') !== false) {
                $params = substr($c, strpos($c, '.')+1);
                foreach ($componentClasses as $i) {
                    if (strpos($params, $i)!==false) {
                        $dependentComponentClasses[] = $c;
                    }
                }
            }
        }
        if ($dependentComponentClasses) {
            echo "dependent componentClasses: ".count($dependentComponentClasses)." (Cc, Trl)\n";
            echo implode(', ', $dependentComponentClasses)."\n";
            self::_clearComponentSettingsCache($dependentComponentClasses, $setting);
        }
    }

    private static function _loadSettingsRecursive(&$settings, $cmpClass)
    {
        echo "$cmpClass is brand new! (not yet in componentClasses)\n";
        Kwf_Component_Settings::$_rebuildingSettings = true;
        $settings[$cmpClass] = Kwf_Component_Settings::_getSettingsIncludingPreComputed($cmpClass);
        Kwf_Component_Settings::$_rebuildingSettings = false;
        foreach (self::_getComponentClassesFromGeneratorsSetting($settings[$cmpClass]['generators']) as $c) {
            if (!isset($settings[$c])) {
                self::_loadSettingsRecursive($settings, $c);
            }
        }
    }

    private static function _removeSettingsRecursive(&$settings, $removedCls)
    {
        echo "removed component class: $removedCls\n";

        $stillUsed = false;
        foreach (Kwc_Abstract::getComponentClasses() as $cls) {
            if (!isset($settings[$cls])) continue;
            if ($cls != $removedCls && in_array($removedCls, self::_getComponentClassesFromGeneratorsSetting($settings[$cls]['generators']))) {
                $stillUsed = true;
                break;
            }
        }
        if (!$stillUsed) {
            echo "not used anymore, removed it really\n";
            $generators = $settings[$removedCls]['generators'];
            unset($settings[$removedCls]);
            foreach (self::_getComponentClassesFromGeneratorsSetting($generators) as $c) {
                self::_removeSettingsRecursive($settings, $c);
            }
        }

    }

    private static function _deleteViewCache(Kwf_Model_Select $s)
    {
        $countDeleted = Kwf_Component_Cache::getInstance()->deleteViewCache($s);
        echo "deleted ".$countDeleted." view cache entries\n";
    }

    private static function _clearAssetsDependencies()
    {
        //there is no cache containting dependencies
    }

    private static function _clearAssetsAll($fileType = null)
    {
        if (!$fileType) {
            self::_clearAssetsAll('js');
            self::_clearAssetsAll('defer.js');
            self::_clearAssetsAll('css');
            self::_clearAssetsAll('printcss');
            return;
        }
        $fileNames = array(
            'cache/assets/output-cache-ids-'.$fileType,
            'build/assets/output-cache-ids-'.$fileType,
        );
        foreach ($fileNames as $fileName) {
            if (file_exists($fileName)) {
                $cacheIds = file($fileName);
                unlink($fileName);
                foreach ($cacheIds as $cacheId) {
                    $cacheId = trim($cacheId);
                    echo $cacheId;
                    if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) echo " [DELETED]";
                    if (Kwf_Assets_BuildCache::getInstance()->remove($cacheId)) echo " [build DELETED]";
                    if (Kwf_Cache_SimpleStatic::_delete(array('as_'.$cacheId.'_gzip', 'as_'.$cacheId.'_deflate'))) echo " [gzip DELETED]";
                    if (Kwf_Assets_Cache::getInstance()->remove($cacheId.'_map')) echo " [map DELETED]";
                    if (Kwf_Assets_BuildCache::getInstance()->remove($cacheId.'_map')) echo " [build map DELETED]";
                    if (Kwf_Cache_SimpleStatic::_delete(array('as_'.$cacheId.'_map_gzip', 'as_'.$cacheId.'_map_deflate'))) echo " [map_gzip DELETED]";
                    echo "\n";
                }
            }
        }

        $fileName = 'build/assets/package-max-mtime-'.$fileType;
        if (file_exists($fileName)) {
            $cacheIds = file($fileName);
            unlink($fileName);
            foreach ($cacheIds as $cacheId) {
                $cacheId = trim($cacheId);
                echo $cacheId;
                if (Kwf_Assets_BuildCache::getInstance()->remove($cacheId)) echo " [DELETED]";
                echo "\n";
            }
        }

        $a = new Kwf_Util_Build_Types_Assets();
        $a->flagAllPackagesOutdated($fileType);

        self::_informDuckcast($fileType);
    }
}
