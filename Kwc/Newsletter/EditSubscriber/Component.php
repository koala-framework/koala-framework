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

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        // Wird von redirect component eingebunden, obwohl sie direkt unter
        // newsletter liegt. Dadurch dass die action '' ist, bleibt die form
        // nach dem abschicken auf der selben seite
        $ret['action'] = '';
        return $ret;
    }

    protected function _initForm()
    {
        parent::_initForm();
        if ($this->_recipient) {
            $this->_form->setId($this->_recipient->id);
        }
    }
}
