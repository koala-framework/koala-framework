<?php
class Kwf_Component_Generator_Unique_TablePage1 extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['pbox'] = array(
            'class' => 'Kwf_Component_Generator_Box_Static',
            'component' => 'Kwc_Basic_Empty_Component',
            'priority' => 3,
            'unique' => true,
            'inherit' => true,
            'box' => 'box'
        );
        return $ret;
    }
}
