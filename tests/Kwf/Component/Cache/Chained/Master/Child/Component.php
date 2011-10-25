<?php
class Kwf_Component_Cache_Chained_Master_Child_Component extends Kwc_Abstract
{
    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_GeneratorRow();
        return $ret;
    }
}
