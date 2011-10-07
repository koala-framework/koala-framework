<?php
class Vpc_Directories_Item_Detail_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['hasModifyItemData'] = true;
        $ret['extConfig'] = 'Vps_Component_Abstract_ExtConfig_None';
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


    public static function modifyItemData(Vps_Component_Data $item)
    {
    }

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Vps_Component_Cache_Meta_Static_GeneratorRow();
        return $ret;
    }
}
