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
        $ret['componentName'] = trlKwfStatic('Domain');
        $ret['flags']['subroot'] = 'domain';
        $ret['flags']['hasHome'] = true;
        $ret['flags']['hasDomain'] = true;
        return $ret;
    }

    public function getDomain()
    {
        return $this->getData()->row->domain;
    }

    public function getPreviewDomain()
    {
        return $this->getData()->row->preview_domain;
    }

    public static function getComponentForHost($host)
    {
        $root = Kwf_Component_Data_Root::getInstance();
        $settings = Kwc_Abstract::getSetting($root->componentClass, 'generators');
        $row = Kwf_Model_Abstract::getInstance($settings['domain']['model'])->getRowByHost($host);
        if (!$row) throw new Kwf_Exception('Domain not found: ' . $host);
        return $root->getComponentByClass(
            'Kwc_Root_DomainRoot_Domain_Component',
            array('id' => '-' . $row->id)
        );
    }

    /**
     * @param Kwf_Component_Data $component data, which is a parent domain component
     * @return Kwc_Root_DomainRoot_Domain_Component
     */
    public static function getDomainComponent(Kwf_Component_Data $component)
    {
        while ($component && !is_instance_of(
            $component->componentClass, 'Kwc_Root_DomainRoot_Domain_Component'
        )) {
            $component = $component->parent;
        }
        return $component;
    }
}
