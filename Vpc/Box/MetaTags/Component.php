<?php
class Vpc_Box_MetaTags_Component extends Vpc_Abstract
{
    protected function _getMetaTagComponents()
    {
        $components = array();
        if ($this->getData()->getPage()) {
            /*
            $components = $this->getData()->getPage()->getRecursiveChildComponents(array(
                'page' => false,
                'flags' => array('metaTags' => true)
            ));*/
            if (Vpc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'metaTags')) {
                $components[] = $this->getData()->getPage();
            }
        }
        return $components;
    }

    protected function _getMetaTags()
    {
        $components = $this->_getMetaTagComponents();
        $ret = array();
        foreach ($components as $component) {
            foreach ($component->getComponent()->getMetaTags() as $name=>$content) {
                if (!isset($ret[$name])) $ret[$name] = '';
                //TODO: bei zB noindex,nofollow anderes trennzeichen
                $ret[$name] .= ' '.$content;
            }
        }
        foreach ($ret as &$i) $i = trim($i);
        if ($this->getData()->getPage()) {
            /*
            $components = $this->getData()->getPage()->getRecursiveChildComponents(array(
                'page' => false,
                'limit' => 1,
                'flags' => array('noIndex' => true)
            ));*/
            if (/*$components || */Vpc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'noIndex')) {
                if (isset($ret['robots'])) {
                    $ret['robots'] .= ',';
                } else {
                    $ret['robots'] = '';
                }
                $ret['robots'] .= 'noindex';
            }
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
            $ret['verify-v1'] = $configVerify->$configDomain;
        }

        $configVerify = Vps_Registry::get('config')->googleSiteVerification;
        if ($configVerify && $configVerify->$configDomain) {
            $ret['google-site-verification'] = $configVerify->$configDomain;
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['metaTags'] = $this->_getMetaTags();
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
