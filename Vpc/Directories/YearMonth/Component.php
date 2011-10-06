<?php
class Vpc_Directories_YearMonth_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['months'] = array(
            'class' => 'Vpc_Directories_YearMonth_Generator',
            'component' => 'Vpc_Directories_YearMonth_Month_Directory_Component',
            'showInMenu' => true,
            //'model' => null,
        );
        $ret['dateColumn'] = null;
        return $ret;
    }
}
