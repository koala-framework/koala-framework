<?php
class Kwf_Controller_Action_Cli_ClearCacheController extends Kwf_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        $options = array(
            'types' => $this->_getParam('type'),
            'output' => !$this->_getParam('silent'),
            'refresh' => true,
        );
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] = $this->_getParam('exclude-type');
        }
        Kwf_Util_ClearCache::getInstance()->clearCache($options);

        if (!$this->_getParam('skip-check-build') && Kwf_Config::getValue('application.id') != 'kwf') {
            if (!file_exists('build')) {
                echo "ERROR: build folder doesn't exist.\n";
            } else if (file_exists('build/version.json')) {
                $v = json_decode(file_get_contents('build/version.json'));
                foreach ($v as $k=>$i) {
                    if ($k == 'web') {
                        if (!file_exists('.git')) {
                            continue;
                        }
                        $git = Kwf_Util_Git::web();
                    } else {
                        $repoName = "$k-lib";
                        if (!file_exists($repoName)) {
                            echo "ERROR: can't find $k\n";
                            continue;
                        }
                        if (!file_exists($repoName.'/.git')) {
                            continue;
                        }
                        $git = new Kwf_Util_Git($repoName);
                    }
                    if (isset($i->rev)) {
                        if ($git->revParse('HEAD') != $i->rev) {
                            echo "ERROR: $k: build version doesn't match git revision.\n";
                            continue;
                        }
                    }
                    echo "OK: build folder is up to date for $k\n";
                }
            }
        }
        exit;
    }

    public function memcacheAction()
    {
        if (!Kwf_Cache_Simple::$memcacheHost) {
            echo "memcache not configured for host\n";
            exit;
        }
        $s = Kwf_Cache_Simple::$memcacheHost.':'.Kwf_Cache_Simple::$memcachePort;
        echo "Clear the complete memcache on $s?\nThis will effect all other webs using this memcache host.\nAre you REALLY sure you want to do that? [N/y]\n";
        $stdin = fopen('php://stdin', 'r');
        $input = trim(strtolower(fgets($stdin, 2)));
        fclose($stdin);
        if (($input == 'y')) {
            Kwf_Cache_Simple::getMemcache()->flush();
            echo "done\n";
            exit;
        }
        exit(1);
    }

    public function mediaAction()
    {
        if ($this->_getParam('all')) {
            echo "clearing media cache, this can take some time...\n";
            Kwf_Media_OutputCache::getInstance()->clean();
            Kwf_Media_MtimeCache::getInstance()->clean();
            echo "done\n";

            $ev = new Kwf_Events_Event_Media_ClearAll('Kwf_Media_OutputCache');
            Kwf_Events_Dispatcher::fireEvent($ev);

        } else if($class = $this->_getParam('class')) {
            echo "clearing media cache, this can take some time...\n";
            Kwf_Media_OutputCache::getInstance()->clear($class);
            Kwf_Media_MtimeCache::getInstance()->clear($class);
            Kwf_Events_Dispatcher::fireEvent(new Kwf_Events_Event_Media_ClearAll('Kwf_Media_OutputCache'));

        } else {
            echo "param missing. --all or --class\n";
        }
        exit;
    }

    public function writeMaintenanceAction()
    {
        Kwf_Util_Maintenance::writeMaintenanceBootstrapSelf();
        exit;
    }

    public function restoreMaintenanceAction()
    {
        Kwf_Util_Maintenance::restoreMaintenanceBootstrapSelf();
        exit;
    }

    public static function getHelpOptions()
    {
        $types = array();
        foreach (Kwf_Util_ClearCache::getInstance()->getTypes() as $t) {
            $types[] = $t->getTypeName();
        }
        return array(
            array(
                'param'=> 'type',
                'value'=> implode(',', $types),
                'valueOptional' => true,
                'help' => 'what to clear'
            )
        );
    }
}
