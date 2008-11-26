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
        $setting = $this->_getSetting('generators');
        $modelName = $setting['domain']['model'];
        $model = Vps_Model_Abstract::getInstance($modelName);
        $path =
            '/' .
            $model->getRow($model->select()->whereEquals('domain', $host))->id .
            $parsedUrl['path'];
        return $path;
    }
}
