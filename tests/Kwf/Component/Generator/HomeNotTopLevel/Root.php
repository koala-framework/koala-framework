<?php
class Vps_Component_Generator_HomeNotTopLevel_Root extends Vpc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Vps_Component_Generator_HomeNotTopLevel_Category';
        $ret['generators']['category']['model'] = new Vps_Model_FnF(
            array('columns' => array('id', 'name'),
                  'data' => array(
                array('id' => 'main', 'name' => 'Hauptmenü'),
                array('id' => 'bottom', 'name' => 'Unten')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
