<?php
class Kwc_Form_Wizard_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        //$ret['generators']['child']['component']['form1'] = 'Form2_Component';
        //$ret['generators']['child']['component']['form2'] = 'Form1_Component';
        $ret['viewCache'] = false;
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        foreach ($ret['keys'] as $key) {
            if ($ret[$key]->getComponent()->isPosted()) {
                $ret['currentForm'] = $ret[$key];
            }
        }

        if (!isset($ret['currentForm'])) {
            $ret['currentForm'] = $ret[$ret['keys'][0]];
        }
        return $ret;
    }
}
