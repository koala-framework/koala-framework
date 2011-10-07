<?php
class Vps_Component_OutputPlaceholdersPlugin_Root_Component extends Vpc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child']['component']['child'] = 'Vps_Component_OutputPlaceholdersPlugin_Root_Child_Component';
        $ret['plugins']['placeholders'] = 'Vps_Component_Plugin_Placeholders';
        return $ret;
    }

    public function getPlaceholders()
    {
        return array('foo' => 'bar');
    }
}
