<?php
class Kwc_Box_MetaTags_Component extends Kwc_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getMetaTagComponents()
    {
        $components = array();
        if ($this->getData()->getPage()) {
            /*
            $components = $this->getData()->getPage()->getRecursiveChildComponents(array(
                'page' => false,
                'flags' => array('metaTags' => true)
            ));*/
            if (Kwc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'metaTags')) {
                $components[] = $this->getData()->getPage();
            }
        }
        return $components;
    }

    protected function _getMetaTags()
    {
        $components = $this->_getMetaTagComponents();
        $ret = array();
        if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') {
            $ret['generator'] = 'Koala Web Framework CMS';
        }
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
            if (/*$components || */Kwc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'noIndex')) {
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
            $host = Kwf_Config::getValue('server.domain');
        }

        $hostParts = explode('.', $host);
        if (count($hostParts) < 2) {
            $configDomain = $host;
        } else {
            $shortParts = array('com', 'co', 'gv', 'or');
            if (count($hostParts) > 2 & in_array($hostParts[count($hostParts)-2], $shortParts)) {
                $hostParts[count($hostParts)-2] = $hostParts[count($hostParts)-3].$hostParts[count($hostParts)-2];
            }
            $configDomain = $hostParts[count($hostParts)-2]  // zB 'vivid-planet'
                            .$hostParts[count($hostParts)-1]; // zB 'com'
        }
        $configVerify = Kwf_Config::getValueArray('verifyV1');
        if ($configVerify && isset($configVerify[$configDomain])) {
            $ret['verify-v1'] = $configVerify[$configDomain];
        }

        $configVerify = Kwf_Config::getValueArray('googleSiteVerification');
        if ($configVerify && isset($configVerify[$configDomain])) {
            $ret['google-site-verification'] = $configVerify[$configDomain];
        }
        return $ret;
    }

    public function getTemplateVars()
    {
        $ret = parent::getTemplateVars();
        $ret['metaTags'] = $this->_getMetaTags();
        return $ret;
    }

    public function getCacheMeta()
    {
        $ret = parent::getCacheMeta();
        foreach ($this->_getMetaTagComponents() as $component) {
            $ret[] = new Kwf_Component_Cache_Meta_Component($component);
        }
        return $ret;
    }
}
