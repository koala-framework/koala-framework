<?php
class Vpc_Chained_Abstract_MasterGenerator extends Vps_Component_Generator_PseudoPage_Static
{
    protected $_inherits = true;
    protected $_loadTableFromComponent = true;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'layout_content';
        return $ret;
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        return $ret;
    }
}
