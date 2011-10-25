<?php
class Kwc_Paging_Abstract_CacheMeta extends Kwf_Component_Cache_Meta_Static_Component
{
    public static function getDeleteDbId($row, $dbId)
    {
        return $dbId . '-%';
    }
}
