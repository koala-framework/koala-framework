<?php
class Kwc_Editable_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterComponentClass = null)
    {
        $ret = parent::getSettings($masterComponentClass);
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['content'] = $this->getData()->getChildComponent('-content');
        return $ret;
    }

    // used here: Kwc_Editable_ComponentsModel
    public function getNameForEdit()
    {
        return $this->getData()->chained->getComponent()->getNameForEdit().
                ' ('.$this->getData()->getLanguage().')';
    }
}
