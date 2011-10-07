<?php
class Kwf_Component_Generator_RecursiveTable2_Root extends Kwf_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['page'] = array(
            'class' => 'Kwf_Component_Generator_Page_Static',
            'component' => 'Kwf_Component_Generator_RecursiveTable2_Table',
        );
        return $ret;
    }
}
?>