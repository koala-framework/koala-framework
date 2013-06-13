<?php
class Kwc_IncludeCode_Page2_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['child'] = array(
            'component' => 'Kwc_IncludeCode_Page2_Child_Component',
            'class' => 'Kwf_Component_Generator_Static',
        );
        return $ret;
    }

    public function getIncludeCode($position)
    {
        if ($position == 'header') {
            return '<meta name="test" content="foo" >';
        }
    }
}
