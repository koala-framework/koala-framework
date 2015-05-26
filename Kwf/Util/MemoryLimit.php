<?php
class Kwf_Util_MemoryLimit
{
    private static $_maxLimit;

    /**
     * Sets the memory limit in megabytes
     * 
     * Does not lower the limit. Considers maximum value constrained by suhosin. 
     * 
     * @param int limit in Megabytes
     * @return bool Whether setting limit was successful
     */
    public static function set($limit)
    {
        if (!is_int($limit)) throw new Kwf_Exception('Limit must be an integer');
        if ($limit <= 0) throw new Kwf_Exception('Not allowed setting memory limit to: ' . $limit);

        $currentLimit = self::convertToMegabyte(ini_get('memory_limit'));
        if ($limit < (int)$currentLimit) return false;

        $value = $limit;
        $maxLimit = self::getMaxLimit();
        if ($maxLimit > 0 && $maxLimit < $limit) {
            $value = $maxLimit;
        }

        ini_set('memory_limit', $value . 'M');
        return $limit == $value;
    }

    /**
     * @return int current limit in Megabytes
     */
    public static function get()
    {
        return self::convertToMegabyte(ini_get('memory_limit'));
    }

    public static function convertToMegabyte($limit)
    {
        $ret = (int)$limit;
        $ending = strtoupper(substr($limit, -1));
        if ($ending == 'G') {
            $ret *= 1024;
        } else if ($ending == 'K') {
            $ret /= 1024;
        } else {
            if ($ending != 'M' && $ret != $limit) {
                throw new Kwf_Exception('Unknown memory limit format: ' . $limit);
            }
        }
        return $ret;
    }

    public static function setMaxLimit($limit)
    {
        if (!is_int($limit)) throw new Kwf_Exception('Limit must be an integer');
        self::$_maxLimit = $limit;
    }

    public static function getMaxLimit()
    {
        $limit = self::$_maxLimit;

        $suhosinLimit = self::convertToMegabyte(ini_get('suhosin.memory_limit'));
        if ($suhosinLimit && ($suhosinLimit < $limit || !$limit)) $limit = $suhosinLimit;

        return $limit;
    }
}
