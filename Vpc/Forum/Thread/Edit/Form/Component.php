<?php
class Vpc_Forum_Thread_Edit_Form_Component extends Vpc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = 'Vpc_Posts_Write_Form_Success_Component';
        $ret['tablename'] = 'Vpc_Forum_Group_Model';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $thread = $this->getData()->parent->parent;
        $this->_form->setId($thread->row->id);
    }
}
