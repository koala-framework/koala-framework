<?php
class Vpc_Master_Assets_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $dep = new Vps_Assets_Dependencies('Frontend');
        $return['assets']['js'] = $dep->getAssetFiles('js');
        $return['assets']['css'] = $dep->getAssetFiles('css');

        return $return;
    }
}
