<?php
class Vps_Component_Cache_Chained_Master_Child_Component extends Vpc_Abstract
{
    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_GeneratorRow();
        return $ret;
    }
}
