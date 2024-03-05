<?php
class Kwf_Component_View_Helper_IncludeCode extends Kwf_Component_View_Helper_Abstract
{
    public function includeCode($position)
    {
        $data = $this->_getView()->component;

        $ret = '';

        if ($position == 'header') {
            if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') {
                $ret .= "<!--\n";
                $ret .= "    This website is powered by Koala Web Framework CMS Version ".Kwf_Config::getValue('application.kwf.version').".\n";
                $ret .= "    Koala Framework is a free open source Content Management Framework licensed under BSD.\n";
                $ret .= "    http://www.koala-framework.org\n";
                $ret .= "-->\n";
            }
            $helper = new Kwf_View_Helper_DebugData();
            $ret .= $helper->debugData();
        }

        $flag = ($position == 'header') ? 'hasHeaderIncludeCode' : 'hasFooterIncludeCode';
        $cmps = $data->getPage()->getRecursiveChildComponents(array('flags'=>array($flag=>true), 'page' => false));
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
                $ev = new Kwf_Events_Event_CreateAssetUrl(get_class($this), $v, $data->getSubroot());
                Kwf_Events_Dispatcher::fireEvent($ev);
                $v = $ev->url;
                $ret .= "<link rel=\"shortcut icon\" href=\"{$v}\" />\n";
            }

            if (!$assetsBoxUsed) {
                //add default assets if there was no box
                $a = new Kwf_View_Helper_Assets();
                $ret .= $a->assets('Frontend', null);
            }

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
        }
        return $ret;
    }
}
