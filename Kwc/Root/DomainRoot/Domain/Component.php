<?php
class Kwc_Root_DomainRoot_Domain_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['category'] = array(
            'class' => 'Kwc_Root_CategoryGenerator',
            'component' => 'Kwc_Root_Category_Component',
            'model' => 'Kwc_Root_CategoryModel'
        );
        $ret['dataClass'] = 'Kwc_Root_DomainRoot_Domain_Data';
        $ret['componentName'] = trlKwf('Domain');
        $ret['flags']['subroot'] = 'domain';
        $ret['flags']['hasHome'] = true;
        return $ret;
    }

    public static function getComponentForHost($host)
    {
        $host = str_replace('www.', '', $host);
        $root = Kwf_Component_Data_Root::getInstance();
        $settings = Kwc_Abstract::getSetting($root->componentClass, 'generators');
        $row = Kwf_Model_Abstract::getInstance($settings['domain']['model'])->getRowByHost($host);
        if (!$row) throw new Kwf_Exception('Domain not found: ' . $host);
        return $root->getComponentByClass(
            'Kwc_Root_DomainRoot_Domain_Component',
            array('id' => '-' . $row->id)
        );
    }

}
