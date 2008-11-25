<?php
class Vpc_Root_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['generators']['box'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => array(),
            'inherit' => true,
            'priority' => 0
        );
        $ret['generators']['title'] = array(
            'class' => 'Vps_Component_Generator_Box_Static',
            'component' => 'Vpc_Box_Title_Component',
            'inherit' => true,
            'priority' => 0
        );
        $ret['componentName'] = 'Root';
        return $ret;
    }
}
