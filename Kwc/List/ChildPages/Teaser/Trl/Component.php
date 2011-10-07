<?php
class Kwc_List_ChildPages_Teaser_Trl_Component extends Kwc_Chained_Trl_Component
{
    public static function getSettings($masterClass)
    {
        $ret = parent::getSettings($masterClass);
        $ret['generators']['child']['class'] = 'Kwc_List_ChildPages_Teaser_Trl_Generator';
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

    public static function getStaticCacheMeta($componentClass)
    {
        $ret = parent::getStaticCacheMeta($componentClass);
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwf_Component_Model', '{componentId}');
        $ret[] = new Kwf_Component_Cache_Meta_Static_Model('Kwc_Root_Category_Trl_GeneratorModel');
        return $ret;
    }
}
