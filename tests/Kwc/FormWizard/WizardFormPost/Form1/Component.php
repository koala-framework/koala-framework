<?php
class Kwc_FormWizard_WizardFormPost_Form1_Component extends Kwc_Form_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['success'] = false;
        $ret['useAjaxRequest'] = false;
        return $ret;
    }

    public function getSuccessComponent()
    {
        return $this->getData()->parent->getChildComponent('-form2');
    }
}
