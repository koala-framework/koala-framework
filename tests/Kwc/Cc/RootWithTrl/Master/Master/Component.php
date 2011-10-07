<?php
class Vpc_Cc_RootWithTrl_Master_Master_Component extends Vpc_Root_TrlRoot_Master_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        unset($ret['generators']['flag']);
        unset($ret['generators']['box']);
        // man braucht categories, damit Vpc_Root_Category_Trl_Component verwendet wird
        $ret['generators']['category']['component'] = 'Vpc_Cc_RootWithTrl_Master_Master_Category_Component';
        $ret['generators']['category']['model'] = new Vpc_Root_CategoryModel(array(
            'pageCategories' => array('main' => 'Hauptmenü')
        ));
        return $ret;
    }
}