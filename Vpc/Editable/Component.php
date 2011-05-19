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

    // Wird hier verwendet: Vpc_Editable_ComponentsModel
    public function getName()
    {
        return Vpc_Abstract::getSetting($this->getData()->componentClass, 'componentName');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getData()->getChildComponent('-content');
        return $ret;
    }
}
