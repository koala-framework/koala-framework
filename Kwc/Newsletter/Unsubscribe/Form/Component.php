<?php
class Kwc_Newsletter_Unsubscribe_Form_Component extends Kwc_Form_Component
{
    public $_recipient; //set by Kwc_Newsletter_Unsubscribe_Component

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_Unsubscribe_Form_Success_Component';
        $ret['placeholder']['submitButton'] = trlKwfStatic('Unsubscribe newsletter');
        $ret['viewCache'] = false;
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }
    }

    protected function _afterSave(Kwf_Model_Row_Interface $row)
    {
        parent::_afterSave($row);
        $row->mailUnsubscribe();
    }

    //moved to FrontendForm
    protected final function getParentField()
    {}
}
