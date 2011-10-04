<?php
class Vps_Trl_Model_Vps extends Vps_Trl_Model_Abstract
{
    public function __construct()
    {
        $config['filepath'] = VPS_PATH.'/trl.xml';
        parent::__construct($config);
    }
}
