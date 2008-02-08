<?php
class Vpc_Decorator_Assets_Component extends Vpc_Decorator_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $dep = new Vps_Assets_Dependencies('Frontend');

        $jsFiles = $dep->getAssetFiles('js');
        $cssFiles = $dep->getAssetFiles('css');
        $return['assets']['js'] = array_merge($return['assets']['js'], $jsFiles);
        $return['assets']['css'] = array_merge($return['assets']['css'], $cssFiles);

        return $return;
    }
}
