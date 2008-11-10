<?php
class Vpc_News_Detail_Abstract_Admin extends Vpc_Directories_Item_Detail_Admin
{
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_News_Directory_Row) {
            $components = Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass($this->_class, array('id'=>'_'.$row->id));
            Vps_Component_Cache::getInstance()->remove($components);
        }
    }
}
