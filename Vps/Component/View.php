<?php
class Vps_Component_View extends Zend_View
{
    public function init()
    {
        $this->addScriptPath('');
        $this->addHelperPath(VPS_PATH . '/Vps/View/Helper', 'Vps_View_Helper');
    }
}
