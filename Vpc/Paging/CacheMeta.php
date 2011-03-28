<?php
class Vpc_Paging_CacheMeta extends Vps_Component_Cache_Meta_Static_Component
{
    public static function getDeleteDbId($row, $dbId)
    {
        return $dbId . '-%';
    }
}
