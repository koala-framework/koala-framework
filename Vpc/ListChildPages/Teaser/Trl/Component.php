<?php
class Vpc_ListChildPages_Teaser_Trl_Component extends Vpc_Chained_Trl_Component
{
    public static function getSettings($masterClass)
    {
        $ret = parent::getSettings($masterClass);
        $ret['generators']['child']['class'] = 'Vpc_ListChildPages_Teaser_Trl_Generator';
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
// d($this->getData()->getChildComponents(array(
//             'generator' => 'child'
//         )));
        $ret['children'] = $this->getData()->getChildComponents(array(
            'generator' => 'child'
        ));
        return $ret;
    }
}
