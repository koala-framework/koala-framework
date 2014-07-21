<?php
class Kwc_Chained_Abstract_MasterGenerator extends Kwf_Component_Generator_PseudoPage_Static
{
    protected $_inherits = true;
    protected $_loadTableFromComponent = true;
    protected $_addUrlPart = false;

    public function getPagesControllerConfig($component, $generatorClass = null)
    {
        $ret = parent::getPagesControllerConfig($component, $generatorClass);
        $ret['icon'] = 'layout_content';
        return $ret;
    }

    protected function _init()
    {
        parent::_init();
    }

    public function getGeneratorFlags()
    {
        $ret = parent::getGeneratorFlags();
        $ret['showInPageTreeAdmin'] = true;
        $ret['showInLinkInternAdmin'] = true;
        return $ret;
    }
}
