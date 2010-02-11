<?php
class Vpc_Root_TrlRoot_Master_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['flags']['showInPageTreeAdmin'] = true;
        $ret['flags']['hasHome'] = true;
        return $ret;
    }
}
