<?php
class Vps_Trl_Model_Web extends Vps_Trl_Model_Abstract
{
    public function __construct()
    {
        $config['filepath'] = './application/trl.xml';
        parent::__construct($config);
    }
}