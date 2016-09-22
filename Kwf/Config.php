<?php
class Kwf_Config
{
    public static function getValueArray($var)
    {
        $ret = self::getValue($var);
        if (!is_array($ret)) {
            throw new Kwf_Exception("getValueArray expects $var to be an array");
        }
        return $ret;
    }

    public static function getValue($var)
    {
        $cacheId = 'config-'.$var;
        $ret = Kwf_Cache_SimpleStatic::fetch($cacheId, $success);
        if ($success) {
            return $ret;
        }

        $cfg = Kwf_Config_Web::getInstance();
        foreach (explode('.', $var) as $i) {
            if (!isset($cfg->$i)) {
                $cfg = null;
                break;
            }
            $cfg = $cfg->$i;
        }
        $ret = $cfg;
        if (is_object($ret)) {
            $ret = $ret->toArray();
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
    }
}
