<?php
class Vps_Component_Generator_Domain_DomainModel extends Vpc_Root_DomainRoot_Model
{
    public function __construct($config = array())
    {
        $config['domains'] = array(
                'at' => array('name' => 'Österreich', 'domain' => 'rotary.at'),
                'ch' => array('name' => 'Liechtenstein und Schweiz', 'domain' => 'rotary.ch')
            );
        parent::__construct($config);
    }
}
