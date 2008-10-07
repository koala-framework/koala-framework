<?php
class Vpc_Posts_Detail_Admin extends Vpc_Abstract_Composite_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByDbId($row->component_id.'-'.$row->id);
            Vps_Component_Cache::getInstance()->remove($components);
        }
        // Wenn Benutzer ändert, alle Posts von Benutzer löschen (wg. componentLink auf Benutzer)
        if ($row instanceof Vps_Model_User_User) {
            $table = new Vpc_Posts_Directory_Model();
            $select = $table->select();
            $select->where('user_id = ?', $row->id);
            $select->from($table->info('name'), "CONCAT(component_id, '-', id)");
            Vps_Component_Cache::getInstance()->removeBySelect($select);
        }
    }
}
