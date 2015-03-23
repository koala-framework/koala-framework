<?php
class Kwc_Trl_Domains_DomainsModel extends Kwc_Root_DomainRoot_Model
{
    public function __construct()
    {
        $config = array();
        $config['domains'] = array(
            'at' => array(
                'domain' => 'www.test.at'
            ),
            'hu' => array(
                'domain' => 'www.test.hu'
            ),
            'ro' => array(
                'domain' => 'www.test.ro'
            ),
        );
        parent::__construct($config);
    }
}
