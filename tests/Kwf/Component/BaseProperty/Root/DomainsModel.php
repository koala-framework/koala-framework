<?php
class Kwf_Component_BaseProperty_Root_DomainsModel extends Kwc_Root_DomainRoot_Model
{
    public function __construct()
    {
        parent::__construct(array(
            'domains' => array(
                'at' => array(
                    'name' => 'Österreich',
                    'domain' => "at.example.com",
                ),
                'si' => array(
                    'name' => 'Slowenien',
                    'domain' => "si.example.com",
                )
            )
        ));
    }
}
