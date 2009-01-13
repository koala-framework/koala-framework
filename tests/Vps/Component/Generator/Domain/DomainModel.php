<?php
class Vps_Component_Generator_Domain_Model extends Vpc_Root_DomainRoot_Model
{
    public function __construct($config = array())
    {
        $config['proxyModel'] = new Vps_Model_FnF(
            array('data' => array(
                array('id' => 'at', 'name' => 'Österreich', 'domain' => 'rotary.at', 'component' => 'at'),
                array('id' => 'ch', 'name' => 'Liechtenstein und Schweiz', 'domain' => 'rotary.ch', 'component' => 'ch')
            ))
        );
        parent::__construct($config);
    }
}
