<?php
class Vpc_User_Detail_GeneralCommunity_LastPosts_Admin extends Vpc_Abstract_Composite_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $userDetails =  Vps_Component_Data_Root::getInstance()
                ->getComponentsByClass('Vpc_User_Detail_Component', array('id'=>'_'.$row->user_id));
            foreach ($userDetails as $detail) {
                Vps_Component_Cache::getInstance()->remove($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)))
            }
        }
        if ($row instanceof Vpc_Forum_Directory_Row) {
            Vps_Component_Cache::getInstance()->remove($this->_class);
        }
    }
}
