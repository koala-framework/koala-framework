<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Cards_Card_Component extends Kwc_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Cards_Card_Page_Component',
        );
        return $ret;
    }
}
