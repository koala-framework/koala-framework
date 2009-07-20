<?php
class Vpc_Editable_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        $ret['generators']['content'] = array(
            'class' => 'Vps_Component_Generator_Static',
            'component' => 'Vpc_Paragraphs_Component'
        );
        $ret['assetsAdmin']['files'][] = 'vps/Vpc/Editable/Panel.js';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getData()->getChildComponent('-content');
        return $ret;
    }
}
