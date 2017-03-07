<?php
class Kwc_Newsletter_EditSubscriber_Component extends Kwc_Form_Component
{
    protected $_recipient;

    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['placeholder']['submitButton'] = trlKwfStatic('Save');
        $ret['generators']['child']['component']['success'] =
            'Kwc_Newsletter_EditSubscriber_Success_Component';
        $ret['viewCache'] = false;
        $ret['flags']['skipFulltext'] = true;
        $ret['flags']['noIndex'] = true;
        $ret['flags']['processInput'] = true;
        $ret['flags']['passMailRecipient'] = true;
        return $ret;
    }

    public function processInput(array $postData)
    {
        if (!isset($postData['recipient'])) {
            throw new Kwf_Exception_NotFound();
        }
        $this->_recipient = Kwc_Mail_Redirect_Component::parseRecipientParam($postData['recipient']);
        $this->processInput($params);
    }

    protected function _initForm()
    {
        $formClass = Kwc_Admin::getComponentClass($this, 'FrontendForm');
        $this->_form = new $formClass('form', $this->getData()->componentClass, null);
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }
    }
}
