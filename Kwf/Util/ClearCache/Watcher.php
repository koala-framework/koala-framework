<?php
use Kwf\FileWatcher\Event;
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

        require VENDOR_PATH.'/autoload.php';
        $watcher = Kwf\FileWatcher\Watcher::create($watchPaths);
        $watcher->setExcludePatterns(array(
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
            '/Gruntfile.js',
            '/node_modules/*',
            '/.idea/*'
        ));
        $watcher->setQueueSizeLimit(100);
        $watcher->setFollowLinks(true);

        $output = new Symfony\Component\Console\Output\ConsoleOutput();
        $verbosityLevelMap = array(
            Psr\Log\LogLevel::NOTICE => Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL,
            Psr\Log\LogLevel::INFO   => Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL,
            //Psr\Log\LogLevel::DEBUG  => Symfony\Component\Console\Output\OutputInterface::VERBOSITY_NORMAL,
        );
        $watcher->setLogger(new Symfony\Component\Console\Logger\ConsoleLogger($output, $verbosityLevelMap));


        if ($watcher instanceof Kwf\FileWatcher\Backend\Poll) {
            //don't use poll backend, that's too slow
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

        $watcher->addListener('filewatcher.queue_full', function($e) use ($watcher) {
            echo "more than 100 events (".count($e->eventsQueue)."), did you switch branches or something?\n";
            echo "I'm giving up.\n";
            //TODO: clear-cache and restart clear-cache-watcher
            $watcher->stop();
        });

        $watcher->addListener('filewatcher.delete', array(__CLASS__, '_handleEvent'));
        $watcher->addListener('filewatcher.create', array(__CLASS__, '_handleEvent'));
        $watcher->addListener('filewatcher.modify', array(__CLASS__, '_handleEvent'));
        $watcher->addListener('filewatcher.move',   array(__CLASS__, '_handleEvent'));

        $watcher->start();
        exit;
    }

    public static function _handleEvent($event)
    {
        $eventStart = microtime(true);
        Kwf_Cache_Simple::resetZendCache(); //reset to re-fetch namespace
        if (preg_match('#/config([^/]*)?\.ini$#', $event->filename)) { //config.ini, configPoi.ini, config.local.ini (in any directory)
            if ($event instanceof Event\Modify) {

                $section = call_user_func(array(Kwf_Setup::$configClass, 'getDefaultConfigSection'));
                $cacheId = 'config_'.str_replace('-', '_', $section);
                Kwf_Config_Cache::getInstance()->remove($cacheId);
                echo "removed config cache\n";

                $apcCacheId = $cacheId.getcwd();
                $cacheIds = array(
                    $apcCacheId,
                    $apcCacheId.'mtime',
                );
                $simpleCacheStaticPrefixes = array(
                    'config-',
                    'configAr-',
                );

                if (Kwf_Util_Apc::isAvailable()) {
                    self::_clearApcCache(array(
                        'cacheIds' => $cacheIds,
                        'clearCacheSimpleStatic' => $simpleCacheStaticPrefixes
                    ));
                    echo "cleared apc config cache\n";
                } else {
                    foreach ($simpleCacheStaticPrefixes as $i) {
                        Kwf_Cache_SimpleStatic::clear($i);
                    }
                }

                $cmd = Kwf_Config::getValue('server.phpCli')." bootstrap.php clear-cache --type=setup";
                exec($cmd, $out, $ret);
                echo "cleared setup.php cache";
                if ($ret) echo " [FAILED]";
                echo "\n";
            }
        } else if (preg_match('#(Acl|MenuConfig)\.php$#', $event->filename)) {
            if ($event instanceof Event\Modify) {
                Kwf_Acl::clearCache();
                echo "cleared acl cache...\n";
            }
        } else if (self::_endsWith($event->filename, '.twig')) {
            $twig = new Kwf_View_Twig_Environment();
            $cacheFile = $event->filename;
            $cacheFile = substr($cacheFile, strlen(getcwd())+1);
            $cacheFile = $twig->getCache(false)->generateKey(null, $twig->getTemplateClass($cacheFile));

            if (file_exists($cacheFile)) {
                unlink($cacheFile);
                echo "cleared twig cache file '$cacheFile' for template '$event->filename'\n";
            }
        }

        if (self::_startsWith($event->filename, getcwd().'/components')
            || self::_startsWith($event->filename, getcwd().'/theme')
            || self::_startsWith($event->filename, KWF_PATH.'/Kwc')
            || (defined('VKWF_PATH') && self::_startsWith($event->filename, VKWF_PATH.'/Vkwc'))
        ) {
            $cls = null;
            foreach (explode(PATH_SEPARATOR, get_include_path()) as $ip) {
                if (substr($ip, 0, 1) == '.') $ip = getcwd().substr($ip, 1);
                if (substr($ip, 0, 1) != '/') $ip = getcwd().'/'.$ip;
                if (self::_startsWith($event->filename, $ip)) {
                    $cls = str_replace('/', '_', substr($event->filename, strlen($ip)+1, -4));
                }
            }

            if (!$cls) {
                echo "unknown component class?!\n";
                return;
            }
            if (!self::_endsWith($event->filename, '/Component.php')) {
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

            if (self::_endsWith($event->filename, '/Admin.php') ||
                       self::_endsWith($event->filename, '/Master.tpl') ||
                       self::_endsWith($event->filename, '/Component.tpl') ||
                       self::_endsWith($event->filename, '/Partial.tpl') ||
                       self::_endsWith($event->filename, '/Master.twig') ||
                       self::_endsWith($event->filename, '/Component.twig') ||
                       self::_endsWith($event->filename, '/Partial.twig') ||
                       self::_endsWith($event->filename, '/Controller.php') ||
                       self::_endsWith($event->filename, '/FrontendForm.php') ||
                       self::_endsWith($event->filename, '/Form.php') ||
                       self::_endsWith($event->filename, '/Component.css') ||
                       self::_endsWith($event->filename, '/Component.scss')
            ) {
                if ($event instanceof Event\Create || $event instanceof Event\Delete) {
                    echo "recalculate 'componentFiles' setting because comopnent file got removed/added...\n";
                    self::_clearComponentSettingsCache($matchingClasses, 'componentFiles');
                }
            }

            if (   self::_endsWith($event->filename, '/Component.php') ||
                self::_endsWith($event->filename, '/FrontendForm.php') //viewCache for forms is enabled
            ) {
                if ($event instanceof Event\Modify) {
                    self::_clearComponentSettingsCache($matchingClasses);

                    //view cache can depend on settings
                    self::_deleteViewCache(array(array('component_class' => $matchingClasses)));
                }
            } else if (self::_endsWith($event->filename, '/Master.tpl') || self::_endsWith($event->filename, '/Master.twig')) {
                if ($event instanceof Event\Modify) {
                    //all component_classes
                    self::_deleteViewCache(array(array('type'=>'master')));
                }
            } else if (self::_endsWith($event->filename, '/Component.tpl') || self::_endsWith($event->filename, '/Component.twig')) {
                if ($event instanceof Event\Modify) {
                    self::_deleteViewCache(array(array('component_class'=>$matchingClasses, 'type'=>'component')));
                }
            } else if (self::_endsWith($event->filename, '/Partial.tpl') || self::_endsWith($event->filename, '/Partial.twig')) {
                if ($event instanceof Event\Modify) {
                    $s = new Kwf_Model_Select();
                    $s->whereEquals('component_class', $matchingClasses);
                    $s->whereEquals('type', 'partial');
                    self::_deleteViewCache(array(array('component_class'=>$matchingClasses, 'type'=>'partial')));
                }
            } else if (self::_endsWith($event->filename, '/Mail.html.tpl') || self::_endsWith($event->filename, '/Mail.html.twig')) {
                if ($event instanceof Event\Modify) {
                    self::_deleteViewCache(array(array('component_class' => $matchingClasses, 'type'=>'component', 'renderer'=>'mail_html')));
                }
            } else if (self::_endsWith($event->filename, '/Mail.txt.tpl') || self::_endsWith($event->filename, '/Mail.txt.twig')) {
                if ($event instanceof Event\Modify) {
                    self::_deleteViewCache(array(array('component_class' => $matchingClasses, 'type'=>'component', 'renderer'=>'mail_txt')));
                }
            }
        }
        echo "finished in ".round((microtime(true)-$eventStart)*1000, 2)."ms\n\n";
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
            if (isset($newSettings['menuConfig']) && $newSettings['menuConfig'] != $settings[$c]['menuConfig']) {
                $menuConfigChanged = true;
            }
            $settings[$c] = $newSettings;
        }

        echo "refreshed component settings cache...\n";

        if ($dependenciesChanged) {
            echo "assets changed...\n";
            //TODO
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

            Kwf_Component_Cache_Url_Abstract::getInstance()->clear();
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
        if (Kwf_Util_Apc::isAvailable()) {
            self::_clearApcCache(array(
                'clearCacheSimpleStatic' => $clearCacheSimpleStatic,
            ));
        } else {
            foreach ($clearCacheSimpleStatic as $i) {
                Kwf_Cache_SimpleStatic::clear($i);
            }
        }
        echo "\n";

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

    private static function _deleteViewCache($updates)
    {
        $countDeleted = Kwf_Component_Cache::getInstance()->deleteViewCache($updates);
        echo "deleted ".$countDeleted." view cache entries\n";
    }
}
