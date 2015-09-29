<?php
class Kwc_Box_MetaTags_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
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
            if (Kwc_Abstract::getFlag($this->getData()->getPage()->componentClass, 'noIndex')) {
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

    public function injectIntoRenderedHtml($html)
    {
        return self::injectMeta($html, $this->getData()->render());
    }

    //public for trl
    public static function injectMeta($html, $title)
    {
        $startPos = strpos($html, '<!-- metaTags -->');
        $endPos = strpos($html, '<!-- /metaTags -->')+18;
        $html = substr($html, 0, $startPos)
                .$title
                .substr($html, $endPos);
        return $html;
    }
}
