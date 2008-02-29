<?php
class Vpc_Posts_Post_Component extends Vpc_Abstract_Composite_Component
{
    private $_postNum = null;

    public static function getSettings()
    {
        $ret = array_merge(parent::getSettings(), array(
            'tablename'         => 'Vpc_Posts_Model',
            'childComponentClasses' => array(
                'user' => 'Vpc_Posts_Post_UserDetail_Component'
            )
        ));
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $row = $this->getTable()->find($this->getCurrentComponentKey())->current();
        $ret['content'] = $row->content;
        $ret['create_time'] = $row->create_time;
        $ret['postNum'] = $this->_postNum;
        return $ret;
    }

    public function setPostNum($postNum)
    {
        $this->_postNum = $postNum;
    }
}
