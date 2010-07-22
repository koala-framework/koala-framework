<?php
class Vps_Trl_Model_Abstract extends Vps_Model_Xml
{
    public function __construct(array $config = array())
    {
        $config['rootNode'] = 'trl';
        $config['xpath'] = '/trl';
        $config['topNode'] = 'text';
        parent::__construct($config);
    }
}