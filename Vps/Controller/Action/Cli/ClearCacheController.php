<?php
class Vps_Controller_Action_Cli_ClearCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        self::clearCache($this->_getParam('type'), true);
        $this->_helper->viewRenderer->setNoRender(true);
    }

    private static function _getCacheDirs()
    {
        $ret = array();
        foreach (new DirectoryIterator('application/cache') as $d) {
            if ($d->isDir() && substr($d->getFilename(), 0, 1) != '.') {
                $ret[] = $d->getFilename();
            }
        }
        return $ret;
    }

    public static function getHelpOptions()
    {
        $types = array('all', 'memcache', 'view');
        $types = array_merge($types, self::_getCacheDirs());
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to clear'
            )
        );
    }

    public static function clearCache($types = 'all', $output = false)
    {
        if ($types == 'all') {
            $types = array('memcache', 'view');
            $types = array_merge($types, self::_getCacheDirs());
        } else {
            if (!is_array($types)) {
                $types = explode(',', $types);
            }
        }
        if (in_array('memcache', $types)) {
            $cache = Vps_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
            $cache->clean();
            if ($output) echo "cleared memcache...\n";
        }
        if (in_array('view', $types) && Vps_Component_Data_Root::getComponentClass()) {
            Vps_Component_Cache::getInstance()->clean();
            if ($output) echo "cleared view...\n";
        }
        foreach (self::_getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                system("rm -rf application/cache/$d/*");
                if ($output) echo "cleared $d cache...\n";
            }
        }
    }
}
