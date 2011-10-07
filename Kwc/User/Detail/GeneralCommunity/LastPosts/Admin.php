<?php
class Kwc_User_Detail_GeneralCommunity_LastPosts_Admin extends Kwc_Posts_Latest_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Kwc_Posts_Directory_Row) {
            $userDetails =  Kwf_Component_Data_Root::getInstance()
                ->getComponentsByClass('Kwc_User_Detail_Component', array('id'=>'_'.$row->user_id));
            foreach ($userDetails as $detail) {
                Kwf_Component_Cache::getInstance()->remove($detail->getRecursiveChildComponents(array('componentClass'=>$this->_class)));
            }
        }
    }
}
