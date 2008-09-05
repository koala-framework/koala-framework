<?php
class Vpc_User_Detail_GeneralCommunity_LastPosts_Component extends Vpc_Posts_Latest_Component
{
    protected function _getSelect()
    {
        $select = parent::_getSelect();
        $select->where('user_id', $this->getData()->getPage()->row->id);
        return $select;
    }
}
