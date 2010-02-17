<?php
class Vpc_Root_TrlRoot_Generator extends Vps_Component_Generator_PseudoPage_Static
{
    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'font';
        return $ret;
    }
}
