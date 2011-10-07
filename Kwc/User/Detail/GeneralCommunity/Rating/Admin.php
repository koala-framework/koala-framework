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
                p($detail->componentId);
                Vps_Component_Cache::getInstance()->remove($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)));
            }
        }
    }
}
