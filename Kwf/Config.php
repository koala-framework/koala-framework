<?php
class Kwf_Config
{
    public static function getValueArray($var)
    {
        $cacheId = 'configAr-'.$var;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Kwf_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            if (!isset($cfg->$i)) {
                $cfg = null;
                break;
            }
            $cfg = $cfg->$i;
        }
        if ($cfg) {
            $ret = $cfg->toArray();
        } else {
            $ret = array();
        }

        Kwf_Cache_SimpleStatic::add($cacheId, $ret);

        return $ret;
    }

    public static function getValue($var)
    {
        $cacheId = 'config-'.$var;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Kwf_Registry::get('config');
        foreach (explode('.', $var) as $i) {
            if (!isset($cfg->$i)) {
                $cfg = null;
                break;
            }
            $cfg = $cfg->$i;
        }
        $ret = $cfg;
        if (is_object($ret)) {
            throw new Kwf_Exception("this would return an object, use getValueArray instead");
        }

        Kwf_Cache_SimpleStatic::add($cacheId, $ret);

        return $ret;
    }

    /**
     * @internal
     *
     * Delete the config cache for one variable. Needed for some tests.
     */
    public static function deleteValueCache($var)
    {
        Kwf_Cache_SimpleStatic::_delete('config-'.$var);
        Kwf_Cache_SimpleStatic::_delete('configAr-'.$var);
    }
}
