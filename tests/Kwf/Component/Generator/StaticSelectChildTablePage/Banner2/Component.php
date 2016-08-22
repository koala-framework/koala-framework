<?php
class Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['childModel'] = 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Model';
        $ret['generators']['child'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_StaticSelectChildTablePage_Banner2_Child_Component',
        );
        return $ret;
    }
}
