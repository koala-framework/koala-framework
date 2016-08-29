<?php
class Kwc_Cc_RootWithTrl_Master_Master_Component extends Kwc_Root_TrlRoot_Master_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        unset($ret['generators']['flag']);
        unset($ret['generators']['box']);
        // man braucht categories, damit Kwc_Root_Category_Trl_Component verwendet wird
        $ret['generators']['category']['component'] = 'Kwc_Cc_RootWithTrl_Master_Master_Category_Component';
        $ret['generators']['category']['model'] = new Kwc_Root_CategoryModel(array(
            'pageCategories' => array('main' => 'Hauptmenü')
        ));
        return $ret;
    }
}