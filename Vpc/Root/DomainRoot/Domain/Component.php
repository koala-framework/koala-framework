<?php
class Vpc_Root_DomainRoot_Domain_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_DomainRoot_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['componentName'] = trlVps('Domain');
        return $ret;
    }
}
