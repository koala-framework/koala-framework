<?php
/**
 * View, die zum Komponenten-Rendern verwendet wird
 */
class Kwf_Component_View extends Kwf_View
{
    private $_renderer;

    public function __construct(Kwf_Component_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
        parent::__construct();
    }

    public function init()
    {
        parent::init();
        $this->addHelperPath(KWF_PATH . '/Kwf/Component/View/Helper', 'Kwf_Component_View_Helper');
    }

    public function getHelper($name)
    {
        $ret = parent::getHelper($name);
        if ($ret instanceof Kwf_Component_View_Helper_Abstract && $this->_renderer) {
            $ret->setRenderer($this->_renderer);
        }
        return $ret;
    }
}