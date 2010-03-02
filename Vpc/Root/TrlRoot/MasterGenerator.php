<?php
class Vpc_Root_TrlRoot_MasterGenerator extends Vps_Component_Generator_PseudoPage_Static
{
    protected $_inherits = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'font';
        return $ret;
    }
}
