<?php
class Kwf_Component_Generator_HomeNotTopLevel_Root extends Kwc_Root_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Generator_HomeNotTopLevel_Category';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(
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
