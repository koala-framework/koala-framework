<?php
class Vpc_Directories_Month_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['detail'] = array(
            'class' => 'Vpc_Directories_Month_Directory_Generator',
            'component' => 'Vpc_Directories_Month_Detail_Component',
            //'model' => null,
            'showInMenu' => true
        );

        $ret['dateColumn'] = null;

        return $ret;
    }
}
