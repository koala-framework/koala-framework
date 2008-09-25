<?php
class Vpc_User_Detail_GeneralCommunity_Rating_Admin extends Vpc_Admin
{
    public function onRowInsert($row)
    {
        parent::onRowInsert($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $userDetails =  Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_User_Detail_Component', array('id'=>'_'.$row->user_id));
            foreach ($userDetails as $detail) {
                Vps_Component_Cache::getInstance()->remove($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)));
            }
        }
        if ($row instanceof Vpc_Forum_Directory_Row) {
            Vps_Component_Cache::getInstance()->cleanComponentClass($this->_class);
        }
    }
    public function onRowUpdate($row)
    {
        parent::onRowUpdate($row);
        if ($row instanceof Vpc_Forum_Directory_Row) {
            //gruppe offline nehmen
            Vps_Component_Cache::getInstance()->cleanComponentClass($this->_class);
        }
    }
}
