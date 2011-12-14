<?php
/**
 * @package Trl
 * @internal
 */
class Kwf_Trl_Model_Web extends Kwf_Trl_Model_Abstract
{
    public function __construct()
    {
        $config['filepath'] = './trl.xml';
        parent::__construct($config);
    }
}