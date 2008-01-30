<?php
class Vpc_Decorator_Assets_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();
        
        $dep = new Vps_Assets_Dependencies('Frontend');
        
        $cfg = Zend_Registry::get('config');
        if ($cfg->debug->assets) {
            
            $jsFiles = $dep->getAssetFiles('js');
            $cssFiles = $dep->getAssetFiles('css');
        } else {
            $v = $cfg->application->version;
            $jsFiles = array('/assets/AllFrontend.js?v='.$v);
            $cssFiles = array('/assets/AllFrontend.css?v='.$v);
        }
        $return['assets']['js'] = array_merge($return['assets']['js'], $jsFiles);
        $return['assets']['css'] = array_merge($return['assets']['css'], $cssFiles);

        return $return;
    }
}
