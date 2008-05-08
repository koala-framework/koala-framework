<?php
class Vps_View extends Zend_View
{
    public function init()
    {
        $this->addScriptPath(VPS_PATH.'/views');
        $this->setHelperPath(VPS_PATH . '/Vps/View/Helper', 'Vps_View_Helper');
    }
}
