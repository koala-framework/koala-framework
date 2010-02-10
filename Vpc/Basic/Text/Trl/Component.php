<?php
class Vpc_Basic_Text_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentName'] = trlVps('Text');
        $ret['generators']['text'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Basic_Text_Component',
        );
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['text'] = $this->getData()->getChildComponent('-text');
        return $ret;
    }

}
