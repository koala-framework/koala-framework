<?php
class Kwc_User_Detail_GeneralCommunity_Rating_Admin extends Kwc_Admin
{
    public function onRowInsert($row)
    {
        parent::onRowInsert($row);
        if ($row instanceof Kwc_Posts_Directory_Row) {
            $userDetails =  Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass('Kwc_User_Detail_Component', array('id'=>'_'.$row->user_id));
            foreach ($userDetails as $detail) {
                p($detail->componentId);
                Kwf_Component_Cache::getInstance()->remove($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)));
            }
        }
    }
}
