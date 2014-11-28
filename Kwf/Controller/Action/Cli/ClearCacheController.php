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
            'output' => true,
            'refresh' => true,
        );
        if ($this->_getParam('skip-other-servers')) {
            $options['skipOtherServers'] = true;
        }
        if (is_string($this->_getParam('exclude-type'))) {
            $options['excludeTypes'] = $this->_getParam('exclude-type');
        }
        Kwf_Util_ClearCache::getInstance()->clearCache($options);
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
        echo "clearing media cache, this can take some time...\n";
        Kwf_Media_MemoryCache::getInstance()->clean();
        echo "done\n";
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
