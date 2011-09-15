<?php
class Vps_Config
{
    public static function getValueArray($var)
    {
        require_once 'Vps/Cache/Simple.php';

        $cacheId = 'configAr-'.$var;
        $ret = Vps_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Vps_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        if ($cfg) {
            $ret = $cfg->toArray();
        } else {
            $ret = array();
        }

        Vps_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    public static function getValue($var)
    {
        require_once 'Vps/Cache/Simple.php';

        $cacheId = 'config-'.$var;
        $ret = Vps_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Vps_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        $ret = $cfg;
        if (is_object($ret)) {
            throw new Vps_Exception("this would return an object, use getValueArray instead");
        }

        Vps_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    /**
     * Delete the config cache for one variable. Needed for some tests.
     */
    public static function deleteValueCache($var)
    {
        Vps_Cache_Simple::delete('config-'.$var);
        Vps_Cache_Simple::delete('configAr-'.$var);
    }

    public static function clearValueCache()
    {
        require_once 'Vps/Cache/Simple.php';
        Vps_Cache_Simple::clear('config-');
        Vps_Cache_Simple::clear('configAr-');
    }

    public static function checkMasterFiles()
    {
        $masterFiles = array(
            'application/config.ini',
            VPS_PATH . '/config.ini'
        );
        if (file_exists('application/vps_branch')) $masterFiles[] = 'application/vps_branch';
        require_once 'Vps/Config/Web.php';
        $mtime = Vps_Config_Web::getInstanceMtime(Vps_Setup::getConfigSection());
        foreach ($masterFiles as $f) {
            if (filemtime($f) > $mtime) {
                Vps_Config::clearValueCache();
                break;
            }
        }
    }
}
