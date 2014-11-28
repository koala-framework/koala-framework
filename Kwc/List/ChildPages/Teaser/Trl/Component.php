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
}
