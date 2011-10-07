<?php
class Vpc_Root_DomainRoot_Component extends Vpc_Root_Abstract
{
    public static function getSettings()
    {
        $ret = parent::getSettings();
        $ret['generators']['domain'] = array(
            'class' => 'Vpc_Root_DomainRoot_Generator',
            'component' => 'Vpc_Root_DomainRoot_Domain_Component',
            'model' => 'Vpc_Root_DomainRoot_Model'
        );
        return $ret;
    }

    public function formatPath($parsedUrl)
    {
        $host = $parsedUrl['host'];
        $setting = $this->_getSetting('generators');
        $modelName = $setting['domain']['model'];
        $domain = Vps_Model_Abstract::getInstance($modelName)->getRowByHost($host);
        if (!$domain) return null;
        $path =
            '/' .
            $domain->id .
            $parsedUrl['path'];
        return $path;
    }
}
