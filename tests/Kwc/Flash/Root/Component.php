<?php
// /kwf/kwctest/Kwc_Flash_Root_Component/flash
// /kwf/kwctest/Kwc_Flash_Root_Component/community
class Kwc_Flash_Root_Component extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['flash'] = array(
            'component' => 'Kwc_Flash_Flash_Component',
            'class' => 'Kwf_Component_Generator_Page_Static',
        );
        $ret['generators']['community'] = array(
            'component' => 'Kwc_Flash_Community_Component',
            'class' => 'Kwf_Component_Generator_Page_Static',
        );
        unset($ret['generators']['page']);
        unset($ret['generators']['title']);
        unset($ret['generators']['box']);
        return $ret;
    }
}
