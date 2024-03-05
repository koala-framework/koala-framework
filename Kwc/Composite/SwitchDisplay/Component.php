<?php
class Kwc_Composite_SwitchDisplay_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['componentIcon'] = 'shape_move_forwards';
        $ret['componentName'] = trlKwfStatic('Switch display');
        $ret['componentCategory'] = 'layout';
        $ret['componentPriority'] = 60;
        $ret['generators']['child']['component']['linktext'] =
            'Kwc_Composite_SwitchDisplay_LinkText_Component';
        $ret['generators']['child']['component']['content'] = 'Kwc_Paragraphs_Component';
        $ret['rootElementClass'] = 'kwfUp-webStandard';
        $ret['assetsDefer']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwc_Composite_SwitchDisplay_ExtConfig';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        $ret['apiContent'] = 'Kwc_Composite_SwitchDisplay_ApiContent';
        $ret['apiContentType'] = 'switchDisplay';
        return $ret;
    }

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['startOpened'] = $this->getRow()->start_opened;
        return $ret;
    }
}
