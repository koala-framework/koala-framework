<?php
class Kwf_Component_Generator_DbIdRecursiveChildComponents_Page extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['detail'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => 'Kwf_Component_Generator_DbIdRecursiveChildComponents_Detail_Component',
            'dbIdShortcut' => 'foo-',
            'model' => new Kwf_Model_FnF(array(
                'data' => array(
                    array('id' => 1),
                    array('id' => 2),
                    array('id' => 3),
                ),
            ))
        );
        return $ret;
    }
}
