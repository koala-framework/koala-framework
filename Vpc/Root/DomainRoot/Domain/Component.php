<?php
class Vpc_Root_DomainRoot_Domain_Component extends Vpc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Vpc_Root_CategoryGenerator',
            'component' => 'Vpc_Root_Category_Component',
            'model' => 'Vpc_Root_CategoryModel'
        );
        $ret['dataClass'] = 'Vpc_Root_DomainRoot_Domain_Data';
        $ret['componentName'] = trlVps('Domain');
        $ret['flags']['subroot'] = 'domain';
        $ret['flags']['hasHome'] = true;
        return $ret;
    }

    public static function getComponentForHost($host)
    {
        $host = str_replace('www.', '', $host);
        $root = Vps_Component_Data_Root::getInstance();
        $settings = Vpc_Abstract::getSetting($root->componentClass, 'generators');
        $row = Vps_Model_Abstract::getInstance($settings['domain']['model'])->getRowByHost($host);
        if (!$row) throw new Vps_Exception('Domain not found: ' . $host);
        return $root->getComponentByClass(
            'Vpc_Root_DomainRoot_Domain_Component',
            array('id' => '-' . $row->id)
        );
    }

}
