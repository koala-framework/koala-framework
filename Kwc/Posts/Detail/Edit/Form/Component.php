<?php
class Vpc_Posts_Detail_Edit_Form_Component extends Vpc_Posts_Write_Form_Component
{
    protected function _getPostsComponent()
    {
        return $this->_getPostComponent();
    }

    protected function _getPostComponent()
    {
        return $this->getData()->parent->parent->parent;
    }

    protected function _initForm()
    {
        parent::_initForm();
        $this->_form->setId($this->_getPostComponent()->row->id);
    }

    public function processInput($postData)
    {
        if (!$this->_getPostComponent()->getChildComponent('-actions')->getComponent()->mayEditPost()) {
            throw new Vps_Exception_AccessDenied();
        }
        parent::processInput($postData);
    }

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlVpsStatic('save');
        return $ret;
    }
}
