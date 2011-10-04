<?php
class Vpc_Abstract_Composite_MetaHasContent extends Vps_Component_Cache_Meta_Static_Component
{
    public static function getDeleteDbId($row, $dbId)
    {
        $pos = max(strrpos($dbId, '-'), strrpos($dbId, '_'));
        if ($pos) {
            return substr($dbId, 0, $pos);
        }
        return null;
    }
}
