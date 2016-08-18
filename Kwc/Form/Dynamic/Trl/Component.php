<?php
class Kwc_Form_Dynamic_Trl_Component extends Kwc_Abstract_Composite_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);

        //form nicht übersetzen, sondern die exakt gleiche wie im master verwenden
        $g = Kwc_Abstract::getSetting($masterComponentClass, 'generators');
        $ret['generators']['child']['component']['form'] = $g['child']['component']['form'];
        $ret['generators']['child']['masterComponentsMap'][$g['child']['component']['form']] = $g['child']['component']['form'];

        $ret['ownModel'] = 'Kwf_Component_FieldModel';

        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $data = $this->getData();
        $ret['data'] = $data;
        $ret['chained'] = $data->chained;
        $ret['template'] = self::getTemplateFile($data->chained->componentClass);

        $ret['form'] = $this->getData()->getChildComponent('-form');

        return $ret;
    }

    public function getMailSettings()
    {
        $ret = $this->getData()->chained->getComponent()->getMailSettings();
        $row = $this->getData()->getComponent()->getRow();
        if ($row->subject) $ret['subject'] = $row->subject;
        if ($ret['send_confirm_mail'] && $row->confirm_subject) $ret['confirm_subject'] = $row->confirm_subject;
        return $ret;
    }
}
