<?php
class Vpc_Decorator_Assets_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $cfg = Zend_Registry::get('config');
        if ($cfg->debug->assets) {
            $dep = new Vps_Assets_Dependencies($cfg->assets->Frontend);
            $jsFiles = $dep->getAssetFiles('js');
            $cssFiles = $dep->getAssetFiles('css');
        } else {
            $v = $cfg->application->version;
            $jsFiles = array('/assets/AllAdmin.js?v='.$v);
            $cssFiles = array('/assets/AllAdmin.css?v='.$v);
        }
        $return['assets']['js'] = array_merge($return['assets']['js'], $jsFiles);
        $return['assets']['css'] = array_merge($return['assets']['css'], $cssFiles);

        return $return;
    }
}
