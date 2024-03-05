<?php
class Kwc_Root_DomainRoot_Component extends Kwc_Root_Abstract
{
    public static function getSettings($param = null)
    {
        $ret = parent::getSettings($param);
        $ret['generators']['domain'] = array(
            'class' => 'Kwc_Root_DomainRoot_Generator',
            'component' => 'Kwc_Root_DomainRoot_Domain_Component',
            'model' => 'Kwc_Root_DomainRoot_Model'
        );
        $ret['flags']['hasAvailableLanguages'] = true;
        $ret['baseProperties'] = array();
        return $ret;
    }

    public function formatPath($parsedUrl)
    {
        $host = $parsedUrl['host'];
        if (isset($parsedUrl['port']) && $parsedUrl['port'] != 80) {
            $host .= ':' . $parsedUrl['port'];
        }
        $setting = $this->_getSetting('generators');
        $modelName = $setting['domain']['model'];
        $domain = Kwf_Model_Abstract::getInstance($modelName)->getRowByHost($host);
        if (!$domain) return null;
        $path =
            '/' .
            $domain->id .
            $parsedUrl['path'];
        return $path;
    }

    public static function getAvailableLanguages($componentClass)
    {
        $ret = array();
        foreach (Kwc_Root_DomainRoot_Component::getDomains() as $d) {
            if (isset($d['language'])) {
                if (is_array($d['language'])) {
                    $ret = array_merge($ret, $d['language']);
                } else {
                    $ret[] = $d['language'];
                }
            }
        }
        $ret = array_unique($ret);
        return $ret;
    }

    public static function getDomains()
    {
        $availableDomains = Kwf_Config::getValue('kwc.availableDomains');
        if (is_array($availableDomains)) {
            $domains = Kwf_Config::getValueArray('kwc.domains');
            foreach ($availableDomains as $domain) {
                if (!isset($domains[$domain])) continue;
                $ret[$domain] = $domains[$domain];
            }
        } else {
            $ret = Kwf_Config::getValueArray('kwc.domains');
        }
        return $ret;
    }
}
