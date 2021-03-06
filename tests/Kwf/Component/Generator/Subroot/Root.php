<?php
class Kwf_Component_Generator_Subroot_Root extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['component'] = array(
            'at' => 'Kwf_Component_Generator_Subroot_Domain',
            'ch' => 'Kwf_Component_Generator_Subroot_DomainCh'
        );
        $ret['generators']['domain']['model'] = new Kwf_Model_FnF(
            array(
                'columns' => array('id', 'name', 'domain', 'component'),
                'data' => array(
                array('id' => 'at', 'name' => 'Österreich', 'domain' => 'rotary.at', 'component' => 'at'),
                array('id' => 'ch', 'name' => 'Liechtenstein und Schweiz', 'domain' => 'rotary.ch', 'component' => 'ch')
            ))
        );
        unset($ret['generators']['box']);
        return $ret;
    }
}
?>