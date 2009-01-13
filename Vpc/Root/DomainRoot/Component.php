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
        if (!isset($parsedUrl['host']))
            throw new Vps_Exception("Host is missing in url '$url'");
        $host = $parsedUrl['host'];
        if (substr($host, 0, 8) == 'preview.') {
            $host = substr($host, 8);
        }
        $setting = $this->_getSetting('generators');
        $modelName = $setting['domain']['model'];
        $model = Vps_Model_Abstract::getInstance($modelName);
        $domain = null;
        $rows = $model->getRows();
        foreach ($rows as $row) {
            if ($row->domain == $host) $domain = $row;
        }
        if (!$domain) {
            foreach ($rows as $row) {
                if (!$domain && !$row->pattern) $domain = $row;
                if ($row->pattern && preg_match('/' . $row->pattern . '/', $host)
                ) {
                    $domain = $row;
                }
            }
        }
        if (!$domain)
            throw new Vps_Exception("Domain $host not found, please enter in config");
        $path =
            '/' .
            $domain->id .
            $parsedUrl['path'];
        return $path;
    }
}
