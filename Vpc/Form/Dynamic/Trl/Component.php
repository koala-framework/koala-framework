<?php
class Vpc_Form_Dynamic_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass)
    {
        $ret = parent::getSettings($masterComponentClass);

        //form nicht übersetzen, sondern die exakt gleiche wie im master verwenden
        $g = Vpc_Abstract::getSetting($masterComponentClass, 'generators');
        $ret['generators']['child']['component']['form'] = $g['child']['component']['form'];

        return $ret;
    }

    public function getTemplateVars()
    {
        $data = $this->getData();

        $ret['data'] = $data;
        $ret['chained'] = $data->chained;
        $ret['linkTemplate'] = self::getTemplateFile($data->chained->componentClass);

        $ret['form'] = $this->getData()->getChildComponent('-form');

        return $ret;
    }
}
