<?php
class Kwc_Directories_Item_Detail_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['hasModifyItemData'] = true;
        $ret['extConfig'] = 'Kwf_Component_Abstract_ExtConfig_None';
        return $ret;
    }
    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['row'] = $this->getData()->row;
        $ret['item'] = $this->getData();
        $this->getData()->parent->getComponent()->callModifyItemData($ret['item']);
        return $ret;
    }


    public static function modifyItemData(Kwf_Component_Data $item)
    {
    }
}
