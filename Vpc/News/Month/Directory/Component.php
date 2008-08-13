<?php
class Vpc_News_Month_Directory_Component extends Vpc_Directories_ItemPage_Directory_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();

        $ret['generators']['detail'] = array(
            'class' => 'Vpc_News_Month_Directory_Generator',
            'component' => 'Vpc_News_Month_Detail_Component',
            'table' => 'Vpc_News_Directory_Model',
            'showInMenu' => true
        );

        //für News-Kategorien Box
        $ret['categoryChildId'] = 'month';
        $ret['categoryName'] = trlVps('Months');

        return $ret;
    }

}
