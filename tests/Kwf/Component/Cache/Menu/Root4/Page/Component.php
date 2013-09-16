<?php
class Kwf_Component_Cache_Menu_Root4_Page_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['children'] = array(
            'component' => 'Kwf_Component_Cache_Menu_Root4_Page_Child_Component',
            'class' => 'Kwf_Component_Generator_Table',
            'model' => 'Kwf_Component_Cache_Menu_Root4_Page_Model'
        );
        return $ret;
    }
}
