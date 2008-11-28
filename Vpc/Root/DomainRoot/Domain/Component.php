<?php
class Vpc_Root_DomainRoot_Domain_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_DomainRoot_Domain_CategoryGenerator',
            'component' => 'Vpc_Root_DomainRoot_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['dataClass'] = 'Vpc_Root_DomainRoot_Domain_Data';
        $ret['componentName'] = trlVps('Domain');
        return $ret;
    }
}
