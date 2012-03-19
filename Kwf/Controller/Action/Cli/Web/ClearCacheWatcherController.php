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
        /*
- Komponente Foo: neue unterkomponente einstellen die es noch nicht gibt
   -> error, settings cache wird nicht aktuallisiert
   -> unterkomponente wird angelegt gits damit also
   -> ist aber nicht referenziert; shit!
   -> LÖSUNG (ja, es gibt für alles ein lösung)
      *merken* welche komponenten geändert wurde und wo das settings cache refershen einen fehler lieferte
               und bei jeder anderen komponenten settings änderung die auch refreshen - bis es einmal durch ist

- alle verfügaren komponenten klassen
  -> muss gelöscht werden wenn generator geändert
  -> apc wird auch gelöscht
  -> aber der große settings cache wird nicht korrekt aktuallisiert
     -> es müsste:
        o neue klasse beim settings cache hinzugefuegt werden
        o entfernte klasse entfernt werden

x   - assets
x     o individual js/css file
x     o all file
x     o dependencies.ini
x     o file created/deleted (for .../* dependencies)
x     o dependencies from components
      o don't just delete; regenerate (if not done already?)
x   - component view cache
x   - component settings

    - component settings depending on other component settings (cc, trl)
      -> setting wo drin steht wer von was abhängig ist
      -> ist auch für dieses needsParentComponentClass problematisch

x   - automatically detected component files (Admin, Form etc)
x   - config cache
x      o full config (file + apc)
x      o config value (apc)
x      o setup.php
       o slow atm (>3sec)
x   - generated classes (from yml)

x   - url cache, process input cache
x     - wenn generator geändert

x   - view cache
x     -> wenn settings geändert

x   - media
x     -> bild cache löschen wenn dimensions geändert

    - "real-world" development
      -> broken component, parse errors etc

    - events
      -> wenn settings geändert

    - trl
      -> wenn xml datei geändert

    - Mail.*.tpl (master only)

        */
        $bufferUsecs = 10000;

        $watchPaths = array(
            getcwd(),
            KWF_PATH,
            VKWF_PATH,
        );

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

        $cmd = "inotifywait -e modify -e create -e delete -e move -e moved_to -e moved_from -r --monitor --exclude 'magick|\.nfs|\.git|.*\.kate-swp|~|cache|log|temp' ".implode(' ', $watchPaths);
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
                    $proc->terminate();
                    exit;
                }
                foreach ($eventsQueue as $event) {
                    self::_handleEventFork($event);
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

    private static function _handleEventFork($event)
    {
        $eventStart = microtime(true);
        $pid = pcntl_fork();
        if ($pid == -1) {
            die('Konnte nicht verzweigen');
        } else if ($pid) {
            // Wir sind der Vater
            pcntl_wait($status); //Schützt uns vor Zombie Kindern
            //if ($status) exit($status);
        } else {
            // Wir sind das Kind
            self::_handleEvent($event);
            exit(0);
        }
        echo "forked process finished in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
    }

    private static function _handleEvent($event)
    {
        echo "\n";
        if (!preg_match('#^([^ ]+) ([A-Z,_]+) ([^ ]+)$#', trim($event), $m)) {
            echo "unknown event: $event\n";
            return;
        }

        $eventStart = microtime(true);
        $event = $m[2];
        $file = $m[1].$m[3];
        echo "$event $file\n";
        unset($m);
        if (substr($file, -4)=='.css' || substr($file, -3)=='.js' || substr($file, -9)=='.printcss') {
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
                $language = Kwf_Trl::getInstance()->getTargetLanguage(); //TODO: all possible languages
                $cacheId = 'fileContents'.$language.$section.self::_getHostForCacheId();
                    $cacheId .= str_replace(array('/', '.', '-', ':'), array('_', '_', '_', '_'), $section.'-'.$file);
                    $cacheId .= Kwf_Component_Data_Root::getComponentClass();
                echo "remove from assets cache: $cacheId";
                if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) {
                    echo " [DELETED]";
                }
                echo "\n";

                self::_clearAssetsAll(substr($file, strrpos($file, '.')+1));

                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
                return;

            } else if ($event == 'CREATE' || $event == 'DELETE' || $event == 'MOVED_TO' || $event == 'MOVED_FROM') {

                self::_clearAssetsDependencies();

                self::_clearAssetsAll(substr($file, strrpos($file, '.')+1));
                echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
                return;
            } else if (self::_endsWith($file, '/dependencies.ini')) {
                if ($event == 'MODIFY') {

                    self::_clearAssetsDependencies();

                    self::_clearAssetsAll();

                    echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
                    return;
                }
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
                Kwf_Util_Apc::callClearCacheByCli(array(
                    'cacheIds'=>$cacheIds,
                    'clearCacheSimple' => array(
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
            || self::_startsWith($file, VKWF_PATH.'/Vkwc')
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

            $matchingClasses = array();
            try {
                if (class_exists($cls)) {
                    foreach (Kwc_Abstract::getComponentClasses() as $c) {
                        if (is_instance_of($c, $cls)) {
                            $matchingClasses[] = $c;
                        }
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
                       self::_endsWith($file, '/Component.printcss')
            ) {
                if ($event == 'CREATE' || $event == 'DELETE') {
                    echo "recalculate 'componentFiles' setting because Admin.php got removed/added...\n";
                    self::_clearComponentSettingsCache($matchingClasses, 'componentFiles');
                }
            }

            if (self::_endsWith($file, '/Component.php')) {
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
            } else if (self::_endsWith($file, '/Component.css')) {
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
                    //nothing to do atm
                }
            } else if (self::_endsWith($file, '/Mail.txt.tpl')) {
                if ($event == 'MODIFY') {
                    //nothing to do atm
                }
            }
            echo "handled event in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n";
        }
    }

    private static function _clearComponentSettingsCache($componentClasses, $setting = null)
    {
        Kwf_Component_Abstract::resetSettingsCache();

        $cacheId = 'componentSettings'.Kwf_Trl::getInstance()->getTargetLanguage()
                    .'_'.str_replace('.', '_', Kwf_Component_Data_Root::getComponentClass());
        $cache = new Kwf_Assets_Cache(array('checkComponentSettings' => false));
        $settings = $cache->load($cacheId);

        $dependenciesChanged = false;
        $generatorssChanged = false;
        $dimensionsChanged = false;
        foreach ($componentClasses as $c) {
            Kwf_Component_Settings::$_rebuildingSettings = true;
            if ($setting) {
                $newSettings = $settings[$c];
                $newSettings[$setting] = Kwc_Abstract::getSetting($c, $setting);
            } else {
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
            }
            if (isset($newSettings['dimensions']) && $newSettings['dimensions'] != $settings[$c]['dimensions']) {
                $dimensionsChanged = true;
            }
            $settings[$c] = $newSettings;
        }

        $cache->save($settings, $cacheId);
        echo "refreshed component settings cache...\n";

        if ($dependenciesChanged) {
            echo "assets changed...\n";
            self::_clearAssetsDependencies();
            self::_clearAssetsAll();
        }

        $clearCacheSimple = array(
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
            $clearCacheSimple[] = 'url-';
        }

        if ($dimensionsChanged) {
            echo "dimensions changed...\n";
            foreach ($componentClasses as $c) {
                $idPrefix = str_replace(array('.', '>'), array('___', '____'), $c) . '_';
                $clearCacheSimple[] = 'media-output-'.$idPrefix;
                $clearCacheSimple[] = 'media-output-mtime-'.$idPrefix;
                foreach (glob('cache/media/'.$idPrefix.'*') as $f) {
                    echo $f." [DELETED]\n";
                    unlink($f);
                }
            }
        }

        echo "APC: ";
        $r = Kwf_Util_Apc::callClearCacheByCli(array(
            'clearCacheSimple' => $clearCacheSimple
        ));
        echo $r['message']."\n";;
        echo "cleared component settings apc cache...\n";
    }

    private static function _deleteViewCache(Kwf_Model_Select $s)
    {
        $s->whereEquals('deleted', false);

        $model = Kwf_Component_Cache::getInstance()->getModel();
        $cacheIds = array();
        foreach ($model->export(Kwf_Model_Abstract::FORMAT_ARRAY, $s) as $row) {
            $cacheIds[] = Kwf_Component_Cache_Mysql::getCacheId($row['component_id'], $row['type'], $row['value']);
        }
        Kwf_Util_Apc::callClearCacheByCli(array(
            'deleteCacheSimple' => $cacheIds
        ));
        $model->updateRows(array('deleted' => true), $s);
        echo "deleted ".count($cacheIds)." view cache entries\n";
    }

    private static function _getHostForCacheId()
    {
        $hostForCacheId = Kwf_Registry::get('config')->server->domain; //TODO all possible hosts
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
        $language = Kwf_Trl::getInstance()->getTargetLanguage(); //TODO: all possible languages
        $rootComponent = Kwf_Component_Data_Root::getComponentClass();
        foreach ($fileTypes as $fileType) {
            foreach (self::_getAssetsTypes() as $assetsType) {
                foreach (array('none', 'gzip', 'deflate') as $encoding) {
                    $allFile = "all/$section/"
                            .($rootComponent?$rootComponent.'/':'')
                            ."$language/$assetsType.$fileType";
                    $cacheId = md5($allFile.$encoding.self::_getHostForCacheId());
                    echo "remove from assets cache: $cacheId";
                    if (Kwf_Assets_Cache::getInstance()->remove($cacheId)) {
                        echo " [DELETED]";
                    }
                    echo "\n";
                }
            }
        }
    }
}
