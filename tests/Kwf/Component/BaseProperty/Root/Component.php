<?php
class Kwf_Component_BaseProperty_Root_Component extends Kwc_Root_DomainRoot_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain']['model'] = 'Kwf_Component_BaseProperty_Root_DomainsModel';
        $ret['generators']['domain']['component'] = 'Kwf_Component_BaseProperty_Domain_Component';
        return $ret;
    }

    public function getBaseProperty($propertyName)
    {
        if ($propertyName == 'test.foo') {
            return 'bar';
        } else {
            return parent::getBaseProperty($propertyName);
        }
    }
}
