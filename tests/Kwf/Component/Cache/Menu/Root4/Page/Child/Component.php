<?php
class Kwf_Component_Cache_Menu_Root4_Page_Child_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['childPages'] = array(
            'component' => 'Kwc_Basic_Empty_Component',
            'class' => 'Kwf_Component_Generator_Page_Table',
            'model' => 'Kwf_Component_Cache_Menu_Root4_Page_Child_Model',
            'showInMenu' => true
        );
        return $ret;
    }
}
