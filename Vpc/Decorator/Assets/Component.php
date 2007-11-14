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
            $jsFiles = array('/assets/AllFrontend.js');
            $cssFiles = array('/assets/AllFrontend.css');
        }
        $return['assets']['js'] = array_merge($return['assets']['js'], $jsFiles);
        $return['assets']['css'] = array_merge($return['assets']['css'], $cssFiles);

        return $return;
    }
}
