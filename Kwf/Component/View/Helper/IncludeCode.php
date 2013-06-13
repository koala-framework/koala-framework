<?php
class Kwf_Component_View_Helper_IncludeCode extends Kwf_Component_View_Helper_Abstract
{
    public function includeCode($data, $position)
    {
        $ret = '';

        $flag = ($position == 'header') ? 'hasHeaderIncludeCode' : 'hasFooterIncludeCode';
        $cmps = $data->getRecursiveChildComponents(array('flags'=>array($flag=>true)));
        if (Kwc_Abstract::getFlag($data->componentClass, $flag)) {
            $cmps[] = $data;
        }
        foreach ($cmps as $c) {
            $includeCode = $c->getComponent()->getIncludeCode($position);
            if (is_string($includeCode)) {
                $ret .= $includeCode;
            } else if (is_object($includeCode) && $includeCode instanceof Kwf_Component_Data) {
                $componentHelper = new Kwf_Component_View_Helper_Component();
                $componentHelper->setRenderer($this->_getRenderer());
                $componentHelper->setView($this->_getView());
                $ret .= $componentHelper->component($includeCode);
            } else if (is_null($includeCode)) {
            } else {
                throw new Kwf_Exception("invalid getIncludeCode return type");
            }
        }

        if ($position == 'header') {
            if ($v = Kwf_Config::getValue('kwc.favicon')) {
                $ret .= "<link rel=\"shortcut icon\" href=\"{$v}\" />\n";
            }
        }
        return $ret;
    }
}
