<?php
/**
 * View, die zum Komponenten-Rendern verwendet wird
 */
class Vps_Component_View extends Vps_View
{
    private $_renderer;

    public function __construct(Vps_Component_Renderer_Abstract $renderer = null)
    {
        $this->_renderer = $renderer;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->addHelperPath(VPS_PATH . '/Vps/Component/View/Helper', 'Vps_Component_View_Helper');
    }

    public function getHelper($name)
    {
        $ret = parent::getHelper($name);
        if ($ret instanceof Vps_Component_View_Helper_Abstract) {
            $ret->setRenderer($this->_renderer);
        }
        return $ret;
    }
}