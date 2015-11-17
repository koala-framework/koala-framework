<?php
class Kwc_Composite_SwitchDisplay_Component extends Kwc_Abstract_Composite_Component
{
    public static $needsParentComponentClass = true;
    public static function getSettings($parentComponentClass)
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = 'shape_move_forwards';
        $ret['componentName'] = trlKwfStatic('Switch display');
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 60;
        $ret['generators']['child']['component']['linktext'] =
            'Kwc_Composite_SwitchDisplay_LinkText_Component';
        $ret['generators']['child']['component']['content'] =
            $parentComponentClass;
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assetsDefer']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwc_Composite_SwitchDisplay_ExtConfig';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer = null)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['startOpened'] = $this->getRow()->start_opened;
        return $ret;
    }
}
