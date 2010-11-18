<?php
class Vpc_List_ChildPages_Teaser_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterClass)
    {
        $ret = parent::getSettings($masterClass);
        $ret['generators']['child']['class'] = 'Vpc_List_ChildPages_Teaser_Trl_Generator';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['children'] = $this->getData()->getChildComponents(array(
            'generator' => 'child'
        ));
        return $ret;
    }

    public static function getStaticCacheVars()
    {
        $ret = array();
        $ret[] = array(
            'model' => 'Vps_Component_Model'
        );
        $ret[] = array(
            'model' => 'Vpc_Root_Category_Trl_GeneratorModel'
        );
        return $ret;
    }
}
