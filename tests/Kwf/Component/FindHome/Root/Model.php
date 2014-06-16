<?php
class Kwf_Component_FindHome_Root_Model extends Kwc_Root_DomainRoot_Model
{
    public function __construct($config = array())
    {
        $config['domains'] = array(
            'at' => array('name'=>'Österreich', 'domain' => 'kwf.benjamin.at', 'component'=>'', 'pattern'=>''),
            'si' => array('name'=>'Slowenien', 'domain' => 'kwf.benjamin.si', 'component'=>'', 'pattern'=>'')
        );
        parent::__construct($config);
    }
}
