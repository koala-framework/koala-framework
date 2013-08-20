<?php
class Kwf_Controller_Action_Cli_Web_ClearCacheWatcherController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return 'watch filesystem for modification and clear affected caches';
    }

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

    public function indexAction()
    {
        if (Kwf_Component_Cache_Memory::getInstance()->getBackend() instanceof Zend_Cache_Backend_Apc) {
            throw new Kwf_Exception_Client("clear-cache-watcher is not compatible with component cache memory apc backend");
        }
        if (!Kwf_Cache_Simple::getZendCache()) { //false means apc cache
            throw new Kwf_Exception_Client("clear-cache-watcher is not compatible with simple cache apc backend");
        }

        $bufferUsecs = 200*1000;

        $watchPaths = array(
            getcwd(),
            KWF_PATH,
        );
        if (defined('VKWF_PATH')) $watchPaths[] = VKWF_PATH;

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

        $cmd = "inotifywait -e modify -e create -e delete -e move -e moved_to -e moved_from -r --monitor --exclude 'magick|\.nfs|\.git|.*\.kate-swp|~|cache|log|temp/|data/index' ".implode(' ', $watchPaths);
        echo $cmd."\n";
        $descriptorspec = array(
            1 => array("pipe", "w"),
        );
        $proc = new Kwf_Util_Proc($cmd, $descriptorspec);
        $pipe = $proc->pipe(1);
        $eventsQueue = array();
        $lastChange = false;
        while(!feof($pipe)) {

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

                // compress the following into into one event:
                // CREATE web.scssdx1493.new
                // MODIFY web.scssdx1493.new
                // MOVED_FROM web.scssdx1493.new
                // MOVED_TO web.scss
                $eventsQueue = array_values($eventsQueue);
                foreach ($eventsQueue as $k=>$event) {
                    $f = $eventsQueue[$k]['file'];
                    if ($event['event'] == 'MOVED_TO' && $k >= 3) {
                        if ($eventsQueue[$k-1]['event'] == 'MOVED_FROM'
                            && $eventsQueue[$k-2]['event'] == 'MODIFY'
                            && $eventsQueue[$k-3]['event'] == 'CREATE'
                            && substr($eventsQueue[$k-1]['file'], 0, strlen($f)) == $f
                            && substr($eventsQueue[$k-2]['file'], 0, strlen($f)) == $f
                            && substr($eventsQueue[$k-3]['file'], 0, strlen($f)) == $f
                        ) {
                            unset($eventsQueue[$k-1]);
                            unset($eventsQueue[$k-2]);
                            unset($eventsQueue[$k-3]);
                            $eventsQueue[$k]['event'] = 'MODIFY';
                        }
                    }
                }
                foreach ($eventsQueue as $event) {
                    self::_handleEventFork($event['file'], $event['event']);
                }
                $eventsQueue = array();
                $lastChange = false;
            }

            $t = microtime(true);
            $read = array($pipe);
            $write = array();
            $except = array();
            if (!stream_select($read, $write, $except, 0, $bufferUsecs)) {
                //echo "NO waited for ".round(microtime(true)-$t, 2)."s\n";
                continue;
            }
            //echo "YES waited for ".round(microtime(true)-$t, 2)."s\n";
            $event = trim(fgets($pipe));
            if (!$event) {
                continue;
            }
            $eventsQueue[] = $event;

            //if (!$lastChange) $lastChange = microtime(true);
            $lastChange = microtime(true);


        }
        $proc->close();
        exit;
    }

    private static $_queue = array();

    private static function _handleEventFork($file, $event)
    {
        $eventStart = microtime(true);
        $pid = pcntl_fork();
        if ($pid == -1) {
            die('Konnte nicht verzweigen');
        } else if ($pid) {
            self::$_queue = array();
            // Wir sind der Vater
            pcntl_wait($status); //Schützt uns vor Zombie Kindern
            if (!$status) {
                self::$_queue = unserialize(file_get_contents('temp/clear-cache-watcher-queue'));
            }
            //if ($status) exit($status);
        } else {
            $queue = self::$_queue;
            self::$_queue = array();
            // Wir sind das Kind
            self::_handleEvent($file, $event);
            if ($queue) {
                echo "\nprocess queued events: \n";
                foreach ($queue as $i) {
                    //adds it back to queue if still fails
                    self::_handleEvent($i['file'], $i['event']);
                }
            }
            file_put_contents('temp/clear-cache-watcher-queue', serialize(self::$_queue));
            if (count(self::$_queue)) {
                echo "queued events: ".count(self::$_queue)."\n";
            }
            exit(0);
        }
        echo "forked process finished in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
    }
/*
    //called as sub process (NOT forked)
    public function classExistsAction()
    {
        $class = $this->_getParam('class');
        if (@class_exists($class)) {
            exit(0);
        } else {
            exit(1);
        }
    }
*/
    private static function _handleEvent($file, $event)
    {
        echo "\n$event $file\n";
        $eventStart = microtime(true);
        if (substr($file, -4)=='.css' || substr($file, -3)=='.js' || substr($file, -9)=='.printcss' || substr($file, -5)=='.scss') {
            echo "asset modified: $event $file\n";
            if ($event == 'MODIFY') {
                $found = false;
                $paths = Kwf_Config::getValueArray('path'); //TODO: reload when config changes
                foreach ($paths as $type=>$path) {
                    if ($path == '.') $path = getcwd();
                    if (substr($file, 0, strlen($path)) == $path) {
                        $file = $type . substr($file, strlen($path));
                        $found = true;
                        break;
                    }
                }
                if (!$found) {
                    echo "not found in config->path: $file\n";
                    return;
                }

                $section = 'web'; //TODO: where to get all possible sections?
                $languages = Kwf_Trl::getInstance()->getLanguages();
                foreach($languages as $language) {
                    $cacheId = 'fileContents'.$language.$section.self::_getHostForCacheId();
                        $cacheId .= str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $section.'-'.$file);
                        $cacheId .= Kwf_Component_Data_Root::getComponentClass();
                    echo "remove from assets cache: $cacheId";
                    if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) {
                        echo " [DELETED]";
                    }
                    echo "\n";
                }

                $assetsType = substr($file, strrpos($file, '.')+1);
                if ($assetsType == 'scss') $assetsType = 'css';
                self::_clearAssetsAll($assetsType);

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";

            } else if ($event == 'CREATE' || $event == 'DELETE' || $event == 'MOVED_TO' || $event == 'MOVED_FROM') {

                self::_clearAssetsDependencies();

                $assetsType = substr($file, strrpos($file, '.')+1);
                if ($assetsType == 'scss') $assetsType = 'css';
                self::_clearAssetsAll($assetsType);
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

                $cmd = "php bootstrap.php clear-cache --type=setup";
                exec($cmd, $out, $ret);
                echo "cleared setup.php cache";
                if ($ret) echo " [FAILED]";
                echo "\n";

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
            }
        }

        if (self::_startsWith($file, getcwd().'/components')
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
                continue;
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
/*
            $cmd = "php bootstrap.php clear-cache-watcher class-exists --class=$cls";
            system($cmd, $classExists);
            if ($classExists != 0) {
                echo "parse error: $cls\n";
                self::$_queue[] = array(
                    'file' => $file,
                    'event' => $event
                );
                return;
            }
*/
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
                    $s->whereEquals('type', 'component');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Component.yml')) {
                $classFile = 'cache/generated/'.str_replace('_', '/', $cls).'.php';
                if ($event == 'MODIFY') {
                    if (file_exists($classFile)) unlink($classFile); //generate, base setting might have changed
                    Kwf_Component_Settings::$_rebuildingSettings = true;
                    //generates it
                    Kwf_Component_Abstract::hasSetting($cls, 'componentName');
                    Kwf_Component_Settings::$_rebuildingSettings = false;
                    echo "generated $classFile\n";

                    self::_clearComponentSettingsCache($matchingClasses);
                } else if ($event == 'DELETE') {
                    echo "delete $classFile";
                    if (file_exists($classFile)) {
                        unlink($classFile);
                        echo " [DELETED]";
                    }
                    echo "\n";
                }
            } else if (self::_endsWith($file, '/Component.css') || self::_endsWith($file, '/Component.scss') || self::_endsWith($file, '/Component.printcss')) {
                //MODIFY already handled above (assets)
                //CREATE/DELETE also handled above
            } else if (self::_endsWith($file, '/Master.tpl')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    //all component_classes
                    $s->whereEquals('type', 'master');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Component.tpl')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'component');
                    $s->whereEquals('renderer', 'component');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Partial.tpl')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'partial');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Mail.html.tpl')) {
                if ($event == 'MODIFY') {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'component');
                    $s->whereEquals('renderer', 'mail_html');
                    self::_deleteViewCache($s);
                }
            } else if (self::_endsWith($file, '/Mail.txt.tpl')) {
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
        if (isset($params['cacheIds']) && is_array($params['cacheIds'])) {
            $params['cacheIds'] = implode(',', $params['cacheIds']);
        }
        Kwf_Util_Apc::callClearCacheByCli($params, Kwf_Util_Apc::VERBOSE);
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

        $cacheId = 'componentSettings_'.str_replace('.', '_', Kwf_Component_Data_Root::getComponentClass());
        $settings = Kwf_Component_Settings::getAllSettingsCache()->load($cacheId);

        $dependenciesChanged = false;
        $generatorssChanged = false;
        $dimensionsChanged = false;
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
            $settings[$c] = $newSettings;
        }

        Kwf_Component_Settings::getAllSettingsCache()->save($settings, $cacheId);
        echo "refreshed component settings cache...\n";

        if ($dependenciesChanged) {
            echo "assets changed...\n";
            self::_clearAssetsDependencies();
            self::_clearAssetsAll();
        }

        $clearCacheSimple = array();
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
            $clearCacheSimple[] = 'url-';
            foreach ($newChildComponentClasses as $cmpClass) {
                if (!in_array($cmpClass, Kwc_Abstract::getComponentClasses())) {
                    self::_loadSettingsRecursive($settings, $cmpClass);
                    Kwf_Component_Settings::getAllSettingsCache()->save($settings, $cacheId);
                }
            }
            $removedComponentClasses = array_diff($oldChildComponentClasses, $newChildComponentClasses);
            foreach ($removedComponentClasses as $removedCls) {
                self::_removeSettingsRecursive($settings, $removedCls);
                Kwf_Component_Settings::getAllSettingsCache()->save($settings, $cacheId);
            }
        }
        echo "cleared component settings apc cache...\n";
        self::_clearApcCache(array(
            'clearCacheSimpleStatic' => $clearCacheSimpleStatic,
            '$clearCacheSimple' => $clearCacheSimple
        ));

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

    private static function _getHostForCacheId()
    {
        $hostForCacheId = Kwf_Registry::get('config')->server->domain; //TODO all possible hosts
        if (!$hostForCacheId && file_exists('cache/lastdomain')) {
            //this file gets written in Kwf_Setup to make it "just work"
            $hostForCacheId = file_get_contents('cache/lastdomain');
        }
        if (preg_match('#[^\.]+\.[^\.]+$#', $hostForCacheId, $m)) {
            $hostForCacheId = $m[0];
        }
        $hostForCacheId = str_replace(array('.', '-', ':'), array('', '', ''), $hostForCacheId);
        return $hostForCacheId;
    }


    private static function _getAssetsTypes()
    {
        $ret = array();
        $assetsTypes = array_keys(Kwf_Registry::get('config')->assets->toArray());
        foreach ($assetsTypes as $assetsType) {
            if ($assetsType == 'dependencies') continue;
            $ret[] = $assetsType;
        }
        return $ret;
    }

    private static function _clearAssetsDependencies()
    {
        $rootComponent = Kwf_Component_Data_Root::getComponentClass();
        foreach (self::_getAssetsTypes() as $assetsType) {
            $cacheId = 'dependencies'.str_replace(':', '_', $assetsType).$rootComponent;
            echo "remove from assets cache: $cacheId";
            if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) {
                echo " [DELETED]";
            }
            echo "\n";
        }
    }

    private static function _clearAssetsAll($fileTypes = null)
    {
        if (is_null($fileTypes)) $fileTypes = array('js', 'css', 'printcss');
        if (is_string($fileTypes)) $fileTypes = array($fileTypes);

        $section = 'web'; //TODO: where to get all possible sections?
        $languages = Kwf_Trl::getInstance()->getLanguages();
        $rootComponent = Kwf_Component_Data_Root::getComponentClass();
        foreach($languages as $language) {
            foreach ($fileTypes as $fileType) {
                foreach (self::_getAssetsTypes() as $assetsType) {
                    foreach (array('none', 'gzip', 'deflate') as $encoding) {
                        $allFile = "all/$section/"
                                .($rootComponent?$rootComponent.'/':'')
                                ."$language/$assetsType.$fileType";
                        $cacheId = md5($allFile.$encoding.self::_getHostForCacheId());
                        echo "remove from assets cache: $cacheId (".$allFile.$encoding.self::_getHostForCacheId().")";
                        if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) {
                            echo " [DELETED]";
                        }
                        echo "\n";
                    }
                }
            }
        }
    }
}
