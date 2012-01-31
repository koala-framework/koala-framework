<?php
class Kwc_Newsletter_EditSubscriber_Component extends Kwc_Form_Component
{
    protected $_recipient;

    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['placeholder']['submitButton'] = trlKwfStatic('Save');
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_EditSubscriber_Success_Component';
        return $ret;
    }

    public function processMailRedirectInput($recipient, $params)
    {
        $this->_recipient = $recipient;
        $this->processInput($params);
    }

    protected function _initForm()
    {
        parent::_initForm();
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }
    }
}
