<?php
abstract class Vps_Component_View_Helper_Abstract
{
    protected $_view;
    protected $_renderer;

    public function setView(Vps_View $view)
    {
        $this->_view = $view;
    }

    /**
     * @return Vps_View
     */
    protected function _getView()
    {
        return $this->_view;
    }

    public function setRenderer(Vps_Component_Renderer_Abstract $renderer)
    {
        $this->_renderer = $renderer;
    }

    /**
     * @return Vps_Component_Renderer_Abstract
     */
    protected function _getRenderer()
    {
        return $this->_renderer;
    }

}
