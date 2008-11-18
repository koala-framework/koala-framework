<?php
class Vpc_Box_Title_Table extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['foo'] = array(
            'class' => 'Vps_Component_Generator_Page_Table',
            'component' => 'Vpc_Basic_Empty_Component',
            'nameColumn' => 'name',
            'model' => 'Vpc_Box_Title_TableModel'
        );
        return $ret;
    }
}
