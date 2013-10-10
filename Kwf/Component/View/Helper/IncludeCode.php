<?php
class Kwf_Component_View_Helper_IncludeCode extends Kwf_Component_View_Helper_Abstract
{
    public function includeCode($position)
    {
        $data = $this->_getView()->component;

        $ret = '';

        $flag = ($position == 'header') ? 'hasHeaderIncludeCode' : 'hasFooterIncludeCode';
        $cmps = $data->getRecursiveChildComponents(array('flags'=>array($flag=>true)));
        if (Kwc_Abstract::getFlag($data->componentClass, $flag)) {
            $cmps[] = $data;
        }
        $statisticsBoxUsed = false;
        $assetsBoxUsed = false;
        foreach ($cmps as $c) {
            $includeCode = $c->getComponent()->getIncludeCode($position);
            if (is_string($includeCode)) {
                $ret .= $includeCode;
            } else if (is_object($includeCode) && $includeCode instanceof Kwf_Component_Data) {
                $componentHelper = new Kwf_Component_View_Helper_Component();
                $componentHelper->setRenderer($this->_getRenderer());
                $componentHelper->setView($this->_getView());
                $ret .= $componentHelper->component($includeCode);

                if (is_instance_of($includeCode->componentClass, 'Kwc_Statistics_Analytics_Component') ||
                    is_instance_of($includeCode->componentClass, 'Kwc_Statistics_Piwik_Component')
                ) {
                    $statisticsBoxUsed = true;
                }
                if (is_instance_of($includeCode->componentClass, 'Kwc_Box_Assets_Component')) {
                    $assetsBoxUsed = true;
                }

            } else if (is_null($includeCode)) {
            } else {
                throw new Kwf_Exception("invalid getIncludeCode return type");
            }
        }

        if ($position == 'header') {
            if ($v = Kwf_Config::getValue('kwc.favicon')) {
                $ret .= "<link rel=\"shortcut icon\" href=\"{$v}\" />\n";
            }

            if (!$assetsBoxUsed) {
                //add default assets if there was no box
                $a = new Kwf_View_Helper_Assets();
                $ret .= $a->assets('Frontend');
            }

            $ret .= Kwf_View_Helper_DebugData::debugData();

            $helper = new Kwf_Component_View_Helper_Dynamic();
            $helper->setRenderer($this->_getRenderer());
            $helper->setView($this->_getView());
            $ret .= $helper->dynamic('SessionToken');

        } else if ($position == 'footer') {
            if (!$statisticsBoxUsed) {
                //if there was no statistics box output default code
                //box is required for eg. multidomains
                $cfg = Kwf_Config::getValueArray('statistics');
                if (isset($cfg['analytics']['code']) && $cfg['analytics']['code']) {
                    throw new Kwf_Exception('To support analytics add Kwc_Statistics_Analytics_Component as a box.');
                }
                if (isset($cfg['piwik']['id']) && $cfg['piwik']['id']) {
                    throw new Kwf_Exception('To support piwik add Kwc_Statistics_Piwik_Component as a box.');
                }
            }

            //see http://nexxar.wordpress.com/2010/10/07/speeding-up-jquery-ready-on-ie/
            $ret .= "\n";
            $ret .= "<script type=\"text/javascript\">\n";
            $ret .= "    if (Ext && Ext.isIE8 && jQuery) jQuery.ready();\n";
            $ret .= "</script>\n";

        }
        return $ret;
    }
}
