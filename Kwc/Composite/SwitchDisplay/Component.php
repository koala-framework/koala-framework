<?php
class Kwc_Composite_SwitchDisplay_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['componentIcon'] = new Kwf_Asset('shape_move_forwards');
        $ret['componentName'] = trlKwfStatic('Switch display');
        $ret['generators']['child']['component']['linktext'] =
            'Kwc_Composite_SwitchDisplay_LinkText_Component';
        $ret['generators']['child']['component']['content'] =
            'Kwc_Paragraphs_Component';
        $ret['cssClass'] = 'webStandard';
        $ret['assets']['dep'][] = 'KwfSwitchDisplay';
        $ret['extConfig'] = 'Kwc_Composite_SwitchDisplay_ExtConfig';
        $ret['ownModel'] = 'Kwf_Component_FieldModel';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['startOpened'] = $this->getRow()->start_opened;
        return $ret;
    }
}
