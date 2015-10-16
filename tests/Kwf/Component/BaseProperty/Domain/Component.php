<?php
class Kwf_Component_BaseProperty_Domain_Component extends Kwc_Root_DomainRoot_Domain_Component
{
    public function getBaseProperty($propertyName)
    {
        $data = array(
            'at' => array(
                'language' => 'de',
                'test.foo' => 'at',
            ),
            'si' => array(
                'language' => 'sl',
                'test.foo' => 'si',
            )
        );
        if ($propertyName == 'test.foo' || $propertyName == 'language') {
            return $data[$this->getData()->id][$propertyName];
        } else if ($propertyName == 'domain') {
            return $this->getData()->row->domain;
        }
        return null;
    }
}
