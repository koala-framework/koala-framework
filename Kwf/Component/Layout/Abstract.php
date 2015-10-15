<?php
abstract class Kwf_Component_Layout_Abstract
{
    protected $_class;
    public function __construct($class) //for the moment we need class only
    {
        $this->_class = $class;
    }

    protected function _getSetting($name)
    {
        return Kwc_Abstract::getSetting($this->_class, $name);
    }

    /**
     * @return self
     */
    public function getInstance($class)
    {
        static $i = array();
        if (!isset($i[$class])) {
            if (!Kwc_Abstract::hasSetting($class, 'layoutClass')) {
                throw new Kwf_Exception("No layoutClass set for '$class'");
            }
            $layout = Kwc_Abstract::getSetting($class, 'layoutClass');
            $i[$class] = new $layout($class);
        }
        return $i[$class];
    }

    public function getSupportedContexts()
    {
        return false;
    }

    public function getSupportedChildContexts()
    {
        return false;
    }
/*
    public function getChildContentWidth(Kwf_Component_Data $child)
    {
    }
*/

    public function getChildContexts(Kwf_Component_Data $data, Kwf_Component_Data $child)
    {
        return $this->getContexts($data);
    }

    public function getContexts(Kwf_Component_Data $data)
    {
        if ($data->isPage || isset($data->box)) {
            $componentWithMaster = Kwf_Component_View_Helper_Master::getComponentsWithMasterTemplate($data);
            $last = array_pop($componentWithMaster);
            if ($last && $last['type'] == 'master') {
                $p = $last['data'];
            } else {
                $p = Kwf_Component_Data_Root::getInstance(); // for tests
            }
            return Kwf_Component_MasterLayout_Abstract::getInstance($p->componentClass)->getContexts($data);
        } else {
            $parent = $data->parent;
            if (!$parent) {
                throw new Kwf_Exception("Can't detect contexts");
            }
            return Kwf_Component_Layout_Abstract::getInstance($parent->componentClass)->getChildContexts($parent, $data);
        }
    }
}
