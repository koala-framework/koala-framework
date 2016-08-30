<?php
class Kwc_Directories_AjaxViewTrl_Root_Component extends Kwc_Root_TrlRoot_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['title']);
        $ret['childModel'] = new Kwc_Trl_RootModel(array(
            'de' => 'Deutsch',
            'en' => 'English'
        ));
        $ret['generators']['master']['component'] = 'Kwc_Directories_AjaxViewTrl_Master_Component';
        $ret['generators']['chained']['component'] = 'Kwc_Root_TrlRoot_Chained_Component.Kwc_Directories_AjaxViewTrl_Master_Component';
        return $ret;
    }
}
