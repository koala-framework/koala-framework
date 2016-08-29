<?php
class Kwc_Posts_Detail_Edit_Form_Component extends Kwc_Posts_Write_Form_Component
{
    protected function _getPostsComponent()
    {
        return $this->_getPostComponent()->parent;
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
            throw new Kwf_Exception_AccessDenied();
        }
        parent::processInput($postData);
    }

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('save');
        return $ret;
    }
}
