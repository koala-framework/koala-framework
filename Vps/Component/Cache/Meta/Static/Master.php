<?php
// Wenn sich Model ändert, werden alle Master-Templates gelöscht (bei hasContent in MasterModel)
class Vps_Component_Cache_Meta_Static_Master extends Vps_Component_Cache_Meta_Static_Model
{
    public static function getDeleteWhere($pattern, $row, $dirtyColumns, $params)
    {
        return array(
            'type' => 'master'
        );
    }
}