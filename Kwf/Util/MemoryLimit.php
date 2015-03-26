<?
class Kwf_Util_MemoryLimit
{
    /**
     * Sets the memory limit in megabytes
     * 
     * Does not lower the limit. Considers maximum value constrained by suhosin. 
     * 
     * @param int $limit in Megabytes
     */
    public static function set($limit)
    {
        $limit = (int)$limit;
        if ($limit < (int)ini_get('memory_limit')) return;
        $suhosinLimit = (int)ini_get('suhosin.memory_limit');
        if ($suhosinLimit && $suhosinLimit < $limit) $limit = $suhosinLimit;
        if ($limit <= 0) throw new Kwf_Exception('Not allowed setting memory limit to: ' . $limit);
        ini_set('memory_limit', "{$limit}M");
    }

    public static function get()
    {
        return ini_get('memory_limit');
    }
}