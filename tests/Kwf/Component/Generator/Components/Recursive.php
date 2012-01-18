<?php
class Kwf_Component_Generator_Components_Recursive extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['static'] = array(
            'class' => 'Kwf_Component_Generator_Static',
            'component' => 'Kwf_Component_Generator_Components_RecursiveStatic',
        );
        $ret['generators']['table'] = array(
            'class' => 'Kwf_Component_Generator_Table',
            'component' => array(
                'empty' => 'Kwc_Basic_None_Component',
                'recursive' => 'Kwf_Component_Generator_Components_RecursiveTable',
            ),
            'model' => 'Kwf_Model_FnF'
        );
        return $ret;
    }
}
?>