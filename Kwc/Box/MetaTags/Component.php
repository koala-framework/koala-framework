<?php
class Kwc_Box_MetaTags_Component extends Kwc_Abstract_Composite_Component
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['flags']['hasHeaderIncludeCode'] = true;
        $ret['flags']['hasInjectIntoRenderedHtml'] = true;
        return $ret;
    }

    public function getIncludeCode()
    {
        return $this->getData();
    }

    protected function _getMetaTags()
    {
        return self::getMetaTagsForData($this->getData());
    }

    //public for trl
    public static function getMetaTagsForData($data)
    {
        $ret = array();
        if (Kwf_Config::getValue('application.kwf.name') == 'Koala Framework') {
            $ret['generator'] = 'Koala Web Framework CMS';
        }
        if ($data->getPage()) {
            if (Kwc_Abstract::getFlag($data->getPage()->componentClass, 'metaTags')) {
                foreach ($data->getPage()->getComponent()->getMetaTags() as $name=>$content) {
                    if (!isset($ret[$name])) $ret[$name] = '';
                    //TODO: for eg noindex,nofollow other separator
                    $ret[$name] .= ' '.$content;
                }
            }
            if (Kwc_Abstract::getFlag($data->getPage()->componentClass, 'noIndex')) {
                if (isset($ret['robots'])) {
                    $ret['robots'] .= ',';
                } else {
                    $ret['robots'] = '';
                }
                $ret['robots'] .= 'noindex';
            }
        }
        foreach ($ret as &$i) $i = trim($i);
        unset($i);

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

    public function getTemplateVars(Kwf_Component_Renderer_Abstract $renderer)
    {
        $ret = parent::getTemplateVars($renderer);
        $ret['metaTags'] = $this->_getMetaTags();
        $ret['kwfUp'] = Kwf_Config::getValue('application.uniquePrefix') ? Kwf_Config::getValue('application.uniquePrefix').'-' : '';
        return $ret;
    }

    public function injectIntoRenderedHtml($html)
    {
        return self::injectMeta($html, $this->getData()->render());
    }

    //public for trl
    public static function injectMeta($html, $title)
    {
        $kwfUp = Kwf_Config::getValue('application.uniquePrefix') ? Kwf_Config::getValue('application.uniquePrefix').'-' : '';
        $startPos = strpos($html, '<!-- '.$kwfUp.'metaTags -->');
        $endPos = strpos($html, '<!-- /'.$kwfUp.'metaTags -->')+18+strlen($kwfUp);
        $html = substr($html, 0, $startPos)
                .$title
                .substr($html, $endPos);
        return $html;
    }
}
