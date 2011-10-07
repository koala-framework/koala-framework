<?php
class Kwc_Editable_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasResources'] = true;
        $ret['generators']['content'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwc_Paragraphs_Component'
        );
        $ret['assetsAdmin']['files'][] = 'kwf/Kwc/Editable/Panel.js';
        return $ret;
    }

    // Wird hier verwendet: Kwc_Editable_ComponentsModel
    public function getNameForEdit()
    {
        return Kwc_Abstract::getSetting($this->getData()->componentClass, 'componentName');
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['content'] = $this->getData()->getChildComponent('-content');
        return $ret;
    }
}
