<?php
class Kwc_Root_DomainRoot_Component extends Kwc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
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
        foreach (Kwf_Config::getValueArray('kwc.domains') as $d) {
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
}
