<?php
class Vps_Component_Generator_RecursiveTable2_Root extends Vps_Component_NoCategoriesRoot
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['page']);
        $ret['generators']['page'] = array(
            'class' => 'Vps_Component_Generator_Page_Static',
            'component' => 'Vps_Component_Generator_RecursiveTable2_Table',
        );
        return $ret;
    }
}
?>