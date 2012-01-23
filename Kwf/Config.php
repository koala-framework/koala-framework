<?php
class Kwf_Config
{
    public static function getValueArray($var)
    {
        $cacheId = 'configAr-'.$var;
        $ret = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Kwf_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        if ($cfg) {
            $ret = $cfg->toArray();
        } else {
            $ret = array();
        }

        Kwf_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    public static function getValue($var)
    {
        $cacheId = 'config-'.$var;
        $ret = Kwf_Cache_Simple::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Kwf_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            $cfg = $cfg->$i;
        }
        $ret = $cfg;
        if (is_object($ret)) {
            throw new Kwf_Exception("this would return an object, use getValueArray instead");
        }

        Kwf_Cache_Simple::add($cacheId, $ret);

        return $ret;
    }

    /**
     * Delete the config cache for one variable. Needed for some tests.
     */
    public static function deleteValueCache($var)
    {
        Kwf_Cache_Simple::delete('config-'.$var);
        Kwf_Cache_Simple::delete('configAr-'.$var);
    }

    public static function clearValueCache()
    {
        Kwf_Cache_Simple::clear('config-');
        Kwf_Cache_Simple::clear('configAr-');
    }

    public static function checkMasterFiles($masterFiles)
    {
        require_once 'Kwf/Config/Web.php';
        $mtime = Kwf_Config_Web::getInstanceMtime(Kwf_Setup::getConfigSection());
        foreach ($masterFiles as $f) {
            if (filemtime($f) > $mtime) {
                Kwf_Config::clearValueCache();
                break;
            }
        }
    }
}
