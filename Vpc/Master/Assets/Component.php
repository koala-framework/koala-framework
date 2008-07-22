<?php
class Vpc_Master_Assets_Component extends Vpc_Master_Abstract
{
    public function getTemplateVars()
    {
        $return = parent::getTemplateVars();

        $dep = new Vps_Assets_Dependencies();
        $return['assets']['js'] = $dep->getAssetUrls('Frontend', 'js');
        $return['assets']['css'] = $dep->getAssetUrls('Frontend', 'css');

        return $return;
    }
}
