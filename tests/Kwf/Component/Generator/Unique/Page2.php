<?php
class Kwf_Component_Generator_Unique_Page2 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['box2'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_None_Component',
            'unique' => true,
            'inherit' => true,
            'priority' => 3,
            'box' => 'box'
        );
        $ret['generators']['page3'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_Unique_Page3',
            'name' => 'page3'
        );
        return $ret;
    }

}
