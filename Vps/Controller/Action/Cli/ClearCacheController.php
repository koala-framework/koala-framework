<?php
class Vps_Controller_Action_Cli_ClearCacheController extends Vps_Controller_Action_Cli_Abstract
{
    public static function getHelp()
    {
        return "clears all caches";
    }

    public function indexAction()
    {
        $type = $this->_getParam('type');
        if ($type == 'all') {
            $types = array('memcache');
            $types = array_merge($types, self::_getCacheDirs());
        } else {
            $types = explode(',', $type);
        }
        if (in_array('memcache', $types)) {
            $cache = Zend_Cache::factory('Core', 'Memcached', array(
                'lifetime'=>null,
                'automatic_cleaning_factor' => false,
                'automatic_serialization'=>true));
            $cache->clean();
            echo "cleared memcache...\n";
        }
        foreach (self::_getCacheDirs() as $d) {
            if (in_array($d, $types)) {
                system("rm -rf application/cache/$d/*");
                echo "cleared $d cache...\n";
            }
        }
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
        $types = array('all', 'memcache');
        $types = array_merge($types, self::_getCacheDirs());
        return array(
            array(
                'param'=> 'type',
                'value'=> $types,
                'valueOptional' => true,
                'help' => 'what to parse'
            )
        );
    }
}
