<?php
class Vpc_Box_MetaTags_Component extends Vpc_Abstract
{
    protected function _getMetaTagComponents()
    {
        $components = array();
        /*
        $components = $this->getData()->getPage()->getRecursiveChildComponents(array(
            'page' => false,
            'flags' => array('metaTags' => true)
        ));*/
        if (Vpc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'metaTags')) {
            $components[] = $this->getData()->getPage();
        }
        return $components;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $components = $this->_getMetaTagComponents();
        $ret['metaTags'] = array();
        foreach ($components as $component) {
            foreach ($component->getComponent()->getMetaTags() as $name=>$content) {
                if (!isset($ret['metaTags'][$name])) $ret['metaTags'][$name] = '';
                //TODO: bei zB noindex,nofollow anderes trennzeichen
                $ret['metaTags'][$name] .= ' '.$content;
            }
        }
        foreach ($ret['metaTags'] as &$i) $i = trim($i);
        /*
        $components = $this->getData()->getPage()->getRecursiveChildComponents(array(
            'page' => false,
            'limit' => 1,
            'flags' => array('noIndex' => true)
        ));*/
        if (/*$components || */Vpc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'noIndex')) {
            if (isset($ret['metaTags']['robots'])) {
                $ret['metaTags']['robots'] .= ',';
            } else {
                $ret['metaTags']['robots'] = '';
            }
            $ret['metaTags']['robots'] .= 'noindex';
        }

        // verify-v1
        if (isset($_SERVER['HTTP_HOST'])) {
            $host = $_SERVER['HTTP_HOST'];
        } else {
            $host = Vps_Registry::get('config')->server->domain;
        }
        $hostParts = explode('.', $host);
        $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                        .$hostParts[count($hostParts)-1]; // zB 'com'
        $configVerify = Vps_Registry::get('config')->verifyV1;
        if ($configVerify && $configVerify->$configDomain) {
            $ret['metaTags']['verify-v1'] = $configVerify->$configDomain;
        }
        return $ret;
    }

    public function getCacheVars()
    {
        $ret = parent::getCacheVars();
        foreach ($this->_getMetaTagComponents() as $component) {
            $ret = array_merge($ret, $component->getComponent()->getMetaTagCacheVars());
        }
        return $ret;
    }
}
