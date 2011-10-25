<?php
class Kwf_Component_Generator_Domain_Domain extends Kwc_Root_DomainRoot_Domain_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category']['component'] = 'Kwf_Component_Generator_Domain_Category';
        $ret['generators']['category']['model'] = new Kwf_Model_FnF(
            array('data' => array(
                array('id' => 'main', 'name' => 'Hauptmenü'),
                array('id' => 'bottom', 'name' => 'Unten')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>