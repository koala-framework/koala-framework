<?php
class Kwc_Directories_Month_Directory_Component extends Kwc_Directories_ItemPage_Directory_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);

        $ret['generators']['detail'] = array(
            'class' => 'Kwc_Directories_Month_Directory_Generator',
            'component' => 'Kwc_Directories_Month_Detail_Component',
            //'model' => null,
            'showInMenu' => true
        );

        $ret['dateColumn'] = null;

        return $ret;
    }

    public static function getViewCacheLifetimeForView()
    {
        //only the current year is shown, so content changes when we have a new year
        return mktime(0, 0, 0, 1, 1, date('Y')+1) - time();
    }
}
