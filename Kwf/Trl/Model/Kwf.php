<?php
/**
 * @package Trl
 * @internal
 */
class Kwf_Trl_Model_Kwf extends Kwf_Trl_Model_Abstract
{
    public function __construct()
    {
        $config['filepath'] = KWF_PATH.'/trl.xml';
        parent::__construct($config);
    }
}
