<?php
class Kwf_Component_OutputPlaceholdersPlugin_Root_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['child']['component']['child'] = 'Kwf_Component_OutputPlaceholdersPlugin_Root_Child_Component';
        $ret['plugins']['placeholders'] = 'Kwf_Component_Plugin_Placeholders';
        $ret['contentWidth'] = 600;
        return $ret;
    }

    public function getPlaceholders()
    {
        return array('foo' => 'bar');
    }
}
