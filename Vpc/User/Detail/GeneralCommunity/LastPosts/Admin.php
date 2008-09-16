<?php
class Vpc_User_Detail_GeneralCommunity_LastPosts_Admin extends Vpc_Abstract_Composite_Admin
{
    public function _onRowAction($row)
    {
        parent::_onRowAction($row);
        if ($row instanceof Vpc_Posts_Directory_Row) {
            $componentId = Vps_Component_Data_Root::getInstance()
                ->getComponentByClass('Vpc_User_Directory_Component')
                ->getChildComponent('_' . $row->user_id)
                ->getChildComponent('-general')
                ->getChildComponent('-latestPosts')->componentId;
            Vps_Component_Cache::getInstance()->remove($this->_class, $componentId);
        }
    }
}
